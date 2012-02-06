<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : ajax.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

global $db, $prefix, $admin_file;
$module_name = basename(dirname(dirname(__FILE__)));
include_once NUKE_MODULES_DIR.$module_name.'/functions.php';
include_once NUKE_MODULES_DIR.$module_name.'/language/lang-'.$currentlang.'.php';

function AddDonation_func(){
	global $db, $prefix, $admin_file, $_GETVAR, $sitename;
	
	$res = $db->sql_query('SELECT currency.* FROM `'.$prefix.'_donations_config`');
	list($currency, $currency_symbol) = $db->sql_fetchrow($res);
	
	$user = (int) $_POST['user_id'];
	$first_name = $_GETVAR->get('first_name', '_POST');
	$last_name = $_GETVAR->get('last_name', '_POST');
	$email = $_GETVAR->get('email', '_POST');
	$amount = (float) $_POST['amount'];
	$event = (int) $_POST['event'];
	
	if ($event!=0){
		$res = $db->sql_query('SELECT `title` FROM `'.$prefix."_donations_events` WHERE `id`='$event'");
		if ($db->sql_numrows($res)==0){
			$event=0;
			$event_title = _DONATIONS_SITE_DONATIONS;
			$itemname = _DONATIONS_TO.' '.$sitename;
		}else{
			list($event_title) = $db->sql_fetchrow($res);
			global $sitename;
			$itemname = _DONATIONS_TO.' '.$sitename.' '._DONATIONS_FOR.' '.$event_title;
		}
	}else{
		$event_title = _DONATIONS_SITE_DONATIONS;
	}
	
	$time = time();
	$db->sql_query('INSERT INTO `'.$prefix."_donations` (`user_id`,`item_name`,`item_number`,`payment_status`,`payment_date`,`mc_gross`,`mc_fee`,`mc_currency`,`settle_amount`,`exchange_rate`,`first_name`,`last_name`,`payer_email`,`currency_symbol`,`event_id`) VALUES ('$user', '$item_name', '$event', 'Completed', '$time', '$amount','0','$currency','$amount','1','$first_name','$last_name','$email','$currency_symbol','$event')");
	$ins_id = $db->sql_nextid();
	$db->sql_query('UPDATE `'.$prefix.'_donations_events` SET `current`=`current`+'.$amount.' WHERE `id`='.$event);
	
	if ($user==1){
		$username = _ANONYMOUS;
	}else{
		list($username) = $db->sql_ufetchrow('SELECT username FROM '.$prefix.'_users WHERE user_id='.$user);
	}
	
	
	echo JSON_encode(array(
		'response'	=> true,
		'user'		=> '<a href="modules.php?name=Profile&mode=viewprofile&u='.$user.'">'.UsernameColor($username).'</a>',
		'status'	=> _DONATIONS_STATUS_COMPLETED,
		'amount'	=> $currency_symbol.currency($amount),
		'date'		=> date('d/m/Y', $time),
		'event'		=> $event_title,
		'link'		=> $admin_file.'.php?op=Donations_View&id='.$ins_id
	));
}

switch ($_GET['func']){
	case 'AddDonation': AddDonation_func(); break;
}

?>