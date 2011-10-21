<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : config.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

function show_form($email = '', $curl = '', $sandbox=false, $currency = 'USD', $ipn_url='', $return_url='', $cancel_url='', $preset_amounts='', $paypal_languages='', $meter_border='', $meter_background='', $meter_text=''){
	if ($curl==''){
		$curl = function_exists('curl_init');
	}
	global $admin_file, $db, $prefix;
	echo '<form action="'.$admin_file.'.php?op=Donations_Config" method="post">';
	echo '<input type="hidden" name="complete" value"1" />';
	echo '<table style="width:500px;border-spacing:0;margin:0 auto;"><tr><th colspan="2">'._DONATIONS_CONFIG_HEADING.'</th></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_PAYPAL_EMAIL.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="email" value="'.$email.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_TRANSFER_METHOD.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><select name="curl" style="width:100%;" />';
	echo '<option value="1" '.(($curl==true) ? ' selected="selected"' : '').'>'._DONATIONS_USE_CURL.'</option>';
	echo '<option value="0"'.(($curl==false) ? ' selected="selected"' : '').'>'._DONATIONS_USE_SOCKETS.'</option>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_SANDBOX.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><select name="sandbox" style="width:100%;" />';
	echo '<option value="1" '.(($sandbox==true) ? ' selected="selected"' : '').'>'._YES.'</option>';
	echo '<option value="0"'.(($sandbox==false) ? ' selected="selected"' : '').'>'._NO.'</option>';
	echo '</select></td></tr>';
	if ((!function_exists('curl_init'))&&(!function_exists('fsockopen'))){
		echo '<tr><td colspan="2" class="row1" style="text-align:center;padding-right:5px;font-weight:bold;">'._DONATIONS_BOTH_METHODS_DISABLED.'</td></tr>';
	}elseif (!function_exists('curl_init')){
		echo '<tr><td colspan="2" class="row1" style="text-align:center;padding-right:5px;font-weight:bold;">'._DONATIONS_CURL_DISABLED.'</td></tr>';
	}elseif (!function_exists('fsockopen')){
		echo '<tr><td colspan="2" class="row1" style="text-align:center;padding-right:5px;font-weight:bold;">'._DONATIONS_SOCKETS_DISABLED.'</td></tr>';
	}
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_PREFFERED_CURRENCY.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><select name="currency" style="width:100%;" />';
	$res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations_currency`');
	while ($row = $db->sql_fetchrow($res)){
		echo '<option value="'.$row['currency'].'" '.(($row['currency']==$currency) ? ' selected="selected"' : '').'>'.$row['currency_symbol'].' '.$row['currency'].'</option>';
	}
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_IPN_URL.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="ipn_url" value="'.$ipn_url.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_RETURN_URL.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="return_url" value="'.$return_url.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_CANCEL_URL.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="cancel_url" value="'.$cancel_url.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_PRESET_AMOUNTS.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="preset_amounts" value="'.$preset_amounts.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_PAYPAL_LANGUAGES.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="paypal_languages" value="'.$paypal_languages.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_METER_BORDER.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="meter_border" value="'.$meter_border.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_METER_BACKGROUND.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="meter_background" value="'.$meter_background.'" style="width:100%;" /></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;font-weight:bold;">'._DONATIONS_METER_TEXT.'</td>';
	echo '<td class="row1" style="width:60%;padding-left: 5px;padding-right:5px;"><input type="text" name="meter_text" value="'.$meter_text.'" style="width:100%;" /></td></tr>';
	
	echo '<tr><td colspan="2" class="row1" style="text-align:center;padding-right:5px;"><input type="submit" value="'._DONATIONS_SAVE_CONFIG.'" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

if (isset($_POST['complete'])){
	global $_GETVAR;
	$email = $_GETVAR->get('email', '_POST', 'email');
	$curl = (int) $_POST['curl'];
	$sandbox = (int) $_POST['sandbox'];
	$currency = $_POST['currency'];
	$ipn_url = $_GETVAR->get('ipn_url', '_POST');
	$return_url = $_GETVAR->get('return_url', '_POST');
	$cancel_url = $_GETVAR->get('cancel_url', '_POST');
	$preset_amounts = $_GETVAR->get('preset_amounts', '_POST');
	$paypal_languages = $_GETVAR->get('paypal_languages', '_POST');
	$meter_border = $_GETVAR->get('meter_border', '_POST');
	$meter_background = $_GETVAR->get('meter_background', '_POST');
	$meter_text = $_GETVAR->get('meter_text', '_POST');
	
	$msg = '';
	if (empty($email)){
		$msg .= _DONATIONS_INVALID_EMAIL.'<br />';
	}
	if (empty($ipn_url)){
		$msg .= _DONATIONS_INVALID_IPN_URL.'<br />';
	}
	if (empty($return_url)){
		$msg .= _DONATIONS_INVALID_RETURN_URL.'<br />';
	}
	if (empty($cancel_url)){
		$msg .= _DONATIONS_INVALID_CANCEL_URL.'<br />';
	}
	if (empty($preset_amounts)){
		$msg .= _DONATIONS_INVALID_PRESET_AMOUNTS.'<br />';
	}
	if (empty($paypal_languages)){
		$msg .= _DONATIONS_INVALID_PAYPAL_LANGUAGES.'<br />';
	}
	if (empty($meter_border)){
		$msg .= _DONATIONS_INVALID_METER_BORDER.'<br />';
	}
	if (empty($meter_background)){
		$msg .= _DONATIONS_INVALID_METER_BACKGROUND.'<br />';
	}
	if (empty($meter_text)){
		$msg .= _DONATIONS_INVALID_METER_TEXT.'<br />';
	}
	
	if (empty($msg)){
		if ($db->sql_query('UPDATE `'.$prefix."_donations_config` SET `receiver_email`='$email', `use_curl`='$curl', `sandbox`='$sandbox', `currency`='$currency', `ipn_url`='$ipn_url', `return_url`='$return_url', `cancel_url`='$cancel_url', `preset_amounts`='$preset_amounts', `paypal_languages`='$paypal_languages', `meter_border`='$meter_border', `meter_background`='$meter_background', `meter_text`='$meter_text'")){
			$msg = _DONATIONS_CONFIG_SUCCESSFUL;
		}else{
			$msg = _DONATIONS_CONFIG_FAILURE;
		}
	}
	
	OpenTable();
	
	echo '<div style="text-align:center;font-weight:bold;">'.$msg.'<br /><br /></div>';
	show_form($email, $curl, $sandbox, $currency, $ipn_url, $return_url, $cancel_url, $preset_amounts, $paypal_languages, $meter_border, $meter_background, $meter_text);
	CloseTable();
}else{
	$res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations_config`');
	$row = $db->sql_fetchrow($res);
	OpenTable();
	show_form($row['receiver_email'], $row['use_curl'], $row['sandbox'], $row['currency'], $row['ipn_url'], $row['return_url'], $row['cancel_url'], $row['preset_amounts'], $row['paypal_languages'], $row['meter_border'], $row['meter_background'], $row['meter_text']);
	CloseTable();
}