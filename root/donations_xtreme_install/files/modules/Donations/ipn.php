<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : ipn.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('NUKE_EVO')||defined('MODULE_FILE')){
	die ("You can't access this file directly...");
}

$module_name = 'Donations';
$currentlang = 'english';

include_once NUKE_MODULES_DIR.$module_name.'/language/lang-'.$currentlang.'.php';
require_once NUKE_MODULES_DIR.$module_name.'/functions.php';

global $adminmail;
global $db, $prefix;
if ($res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations_config`')){
	$dx_config = $db->sql_fetchrow($res);
	$res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations_currency`');
	while ($row = $db->sql_fetchrow($res)){
		$dx_currency[$row['currency']] = $row['currency_symbol'];
	}
}else{
	exit();
}

$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value){
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

$ipn_host = 'www.' . ((isset($_POST['test_ipn'])) ? 'sandbox.' : '') .  'paypal.com';

if ($dx_config['use_curl'] && function_exists('curl_init') && $curl = curl_init('http://'.$ipn_host.'/cgi-bin/webscr')){
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $req);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 4);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
	$errornum = curl_errno($curl);
	$errortext = curl_error($curl);
	if ($errornum){
		ipn_error(_DONATIONS_ERRORS_IPN_CURL.' '.$errornum.': '.$errortext, _DONATIONS_ERRORS_IPN_FAIL_CONNECT);
	}else{
		$res = curl_exec($curl);
		curl_close($curl);
	}
	
}else{
	$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";

	$fp = fsockopen ($ipn_host, 80, $errno, $errstr, 30);
	if (!$fp){
		ipn_error(_DONATIONS_ERRORS_IPN_SOCKET.' '.$errno.': '.$errstr, _DONATIONS_ERRORS_IPN_FAIL_CONNECT);
		exit();
	}

	fputs ($fp, $header . $req);
	while (!feof($fp)) {
		$res = fgets ($fp, 1024);
		if (strcmp ($res, 'VERIFIED') == 0)
			break;
	}
	fclose ($fp);
}

if (strcmp ($res, 'VERIFIED') == 0){
	$verified = 1;
}elseif (strcmp ($res, 'INVALID') == 0){
	$error_str = _DONATIONS_ERRORS_DATA_FROM_PAYPAL.':<br />';
	foreach ($_POST as $key => $val) {
		$error_str .= "$key => $val <br />";
	}
	ipn_error($error_str, _DONATIONS_ERRORS_IPN_INVALID);
	exit();
}

$business = addslashes($_POST['business']);
$quantity = intval($_POST['quantity']);
$item_name = addslashes($_POST['item_name']);
$item_number = addslashes($_POST['item_number']);
$payment_date = addslashes($_POST['payment_date']);
$payer_status = addslashes($_POST['payer_status']);
$payment_status = addslashes($_POST['payment_status']);
$payment_amount = addslashes($_POST['mc_gross']);
$payment_fee = addslashes($_POST['mc_fee']);
$payment_currency = addslashes($_POST['mc_currency']);
$txn_id = addslashes($_POST['txn_id']);
$txn_type = addslashes($_POST['txn_type']);
$receiver_email = addslashes($_POST['receiver_email']);
$payer_email = addslashes($_POST['payer_email']);
$first_name = addslashes($_POST['first_name']);
$last_name = addslashes($_POST['last_name']);
$address_street = addslashes($_POST['address_street']);
$address_city = addslashes($_POST['address_city']);
$address_state = addslashes($_POST['address_state']);
$address_zip = intval($_POST['address_zip']);
$address_country = addslashes($_POST['address_country']);
$invoice = addslashes($_POST['invoice']);
$custom = htmlentities($_POST['custom'], ENT_QUOTES);
$option_seleczion2 = htmlentities($_POST['option_selection2'], ENT_QUOTES);
$memo = addslashes($_POST['memo']);
$tax = addslashes($_POST['tax']);
$option_name1 = addslashes($_POST['option_name1']);
$option_seleczion1 = addslashes($_POST['option_selection1']);
$option_name2 = addslashes($_POST['option_name2']);
$address_status = addslashes($_POST['address_status']);
$pending_reason = addslashes($_POST['pending_reason']);
$payment_type = addslashes($_POST['payment_type']);

if ($payment_currency == $dx_config['currency']  && $payment_status != 'Pending'){
	$settle_amount = $payment_amount - $payment_fee;
	$exchange_rate = '1.00';
}else{
	$settle_amount = addslashes($_POST['settle_amount']);
	$exchange_rate = addslashes($_POST['exchange_rate']);
	if (empty($settle_amount)){
		if (empty($exchange_rate)){
			if ($dx_config['use_curl'] && function_exists('curl_init') && $curl = curl_init('http://download.finance.yahoo.com/d/quotes.csv?s='.$payment_currency .$dx_config['currency'].'=X&f=l1')){
				curl_setopt($curl, CURLOPT_HEADER, 0);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				$exchange_rate = floatval(curl_exec($curl));
				curl_close($curl);
			}else{
				$fp = fsockopen('download.finance.yahoo.com', 80, $errno, $errstr, 30);		
				$out = 'GET /d/quotes.csv?s='.$payment_currency .$dx_config['currency'].'=X&f=l1 HTTP/1.1'."\r\n";
				$out .= 'Host: download.finance.yahoo.com'."\r\n";
				$out .= 'Content-Type: text/html'."\r\n";
				$out .= 'Connection: Close'."\r\n\r\n";
				
				fwrite($fp, $out);
				
				$data = '';
				while(!feof($fp)){
				    $data .= fgets($fp);
				}
				$data = explode("\r\n\r\n",$data);
				$data = explode("\n",$data[1]);
				$exchange_rate = floatval($data[1]);
			}
		}
		$settle_amount = ($payment_amount - $payment_fee)*$exchange_rate;
	}
}

if (strcasecmp($business, $dx_config['receiver_email'])!=0){
	$error = 1;
}

if (!empty($txn_id)){
	$res = $db->sql_query('SELECT `id` FROM `'.$prefix.'_donations` WHERE `txn_id` = \''.$txn_id.'\'');
	$txn_count = $db->sql_numrows($res);
}

if ((!$error)&&($verified == 1)){
	$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id`=\''.((int) $custom).'\'');
	if ($db->sql_numrows($res)!=0){
		$row = $db->sql_fetchrow($res);
		$donator = $row['user_id'];
		$donator_name = $row['username'];
	}else{
		$donator = 0;
	}
	
	if ($payment_status == 'Refunded' || $payment_status == 'Reversed'){
		if ($_POST['parent_txn_id']){
			$res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations` WHERE `txn_id`=\''.$_POST['parent_txn_id'].'\'');
			$transactions_count = $db->sql_numrows($res);
		}

		if ($transactions_count==0){
			$error_str = _DONATIONS_ERRORS_DATA_FROM_PAYPAL.':<br />';
			foreach ($_POST as $key => $val) {
				$error_str .= "$key => $val <br />";
			}
			ipn_error($error_str,_DONATIONS_ERRORS_IPN_REFUND_NO_TXN);
		}elseif ($transactions_count!=1){
			$error_str = _DONATIONS_ERRORS_DATA_FROM_PAYPAL.':<br />';
			foreach ($_POST as $key => $val) {
				$error_str .= "$key => $val <br />";
			}
			ipn_error($error_str,_DONATIONS_ERRORS_IPN_REFUND_MULTIPLE_TXN);
		}else{
			$row = $db->sql_fetchrow($res);
			
			$payment_date = strtotime($payment_date);
			
			$db->sql_query('INSERT INTO `'.$prefix."_donations` (`user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id`) VALUES ('{$row['user_id']}', '$business', '{$_POST['parent_txn_id']}', '{$row['item_name']}', '$item_number', '{$row['quantity']}', '$invoice', '{$row['custom']}', '{$row['tax']}', '$memo', '$payment_status', '$payment_date', '{$row['txn_type']}', '$payment_amount', '$payment_fee', '$payment_currency', '$settle_amount', '{$row['exchange_rate']}', '$first_name', '$last_name', '$payer_email', '{$row['payer_status']}', '{$row['currency_symbol']}', '{$row['event_id']}')");
			
			$db->sql_query('INSERT INTO `'.$prefix.'_donations_archive` (`id`, `user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id`) SELECT `id`,`user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations` WHERE `txn_id`=\''.$_POST['parent_txn_id'].'\'');
			
			$db->sql_query('DELETE FROM `'.$prefix.'_donations` WHERE `txn_id`=\''.$_POST['parent_txn_id'].'\'');	
		}
	}elseif ($payment_status == 'Completed' || $payment_status == 'Pending' && $txn_type == 'web_accept' || $txn_type == 'send_money'){
		if ($txn_count != 0  && $payment_type == 'echeck'){
			$db->sql_query('UPDATE `'.$prefix."_donations` SET `payment_status` = '$payment_status', `mc_fee` = '$payment_fee', `settle_amount` = '$settle_amount', `exchange_rate` = '$exchange_rate' WHERE `txn_id` = '$txn_id'");
		}elseif ($txn_count != 0){
			$error_str = _DONATIONS_ERRORS_DATA_FROM_PAYPAL.':<br />';
			foreach ($_POST as $key => $val) {
				$error_str .= "$key => $val <br />";
			}
			ipn_error($error_str, _DONATIONS_ERRORS_IPN_TXN_IN_USE);
		}else{
			$currency_symbol = $dx_currency[$payment_currency];
			$payment_date = strtotime($payment_date);
			
			if ($item_number!=0){
				$event_id = $item_number;
				$res = $db->sql_query('SELECT `id` FROM `'.$prefix.'_donations_events` WHERE `id`=\''.$event_id.'\'');
				if ($db->sql_numrows($res)>0){
					$db->sql_query('UPDATE `'.$prefix.'_donations_events` SET `current` = `current`+\''.$settle_amount.'\' WHERE `id`=\''.$event_id.'\' LIMIT 1');
				}else{
					$event_id = 0;
				}
			}else{
				$event_id = 0;
			}
			$db->sql_query('INSERT INTO `'.$prefix."_donations` (`user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id`) VALUES ('$donator', '$business', '$txn_id', '$item_name', '$item_number', '$quantity', '$invoice', '$custom', '$tax', '$memo', '$payment_status', '$payment_date', '$txn_type', '$payment_amount', '$payment_fee', '$payment_currency', '$settle_amount', '$exchange_rate', '$first_name', '$last_name', '$payer_email', '$payer_status', '$currency_symbol', '$event_id')");
		}
	}else{
		$error_str = _DONATIONS_ERRORS_DATA_FROM_PAYPAL.':<br />';
		foreach ($_POST as $key => $val) {
			$error_str .= "$key => $val <br />";
		}
		ipn_error($error_str,_DONATIONS_ERRORS_IPN_ACTIVATED_UNWANTED);
	}
}
