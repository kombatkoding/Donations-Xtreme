<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : donate.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('MODULE_FILE')){
	die ("You can't access this file directly...");
}

global $db, $prefix;

$id = (int) $_GET['id'];
if (strcmp($id, $_GET['id'])!=0){
	OpenTable();
	echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_WRONG_ID.'<div>';
	CloseTable();
}else{
	$res = $db->sql_query('SELECT currency.*, config.`sandbox`, config.`receiver_email`, config.`site_target`, config.`meter_border`, config.`meter_background`, config.`meter_text`, config.`donations_per_page`, config.`preset_amounts`, config.`paypal_languages`, config.`ipn_url`, config.`return_url`, config.`cancel_url`, config.`site_active` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
	list($currency, $currency_symbol, $sandbox, $receiver_email, $site_target, $meter_border, $meter_background, $meter_text, $donations_per_page, $preset_amounts, $paypal_languages, $ipn_url, $return_url, $cancel_url, $site_donations_active) = $db->sql_fetchrow($res);
		
	if ($id==0){
		if ($site_donations_active){
			$row = array('title' => _DONATIONS_SITE, 'description' => '', 'target' => $site_target);
			$res = $db->sql_query('SELECT SUM(`settle_amount`) FROM `'.$prefix.'_donations` WHERE `event_id`=0');
			list($amount) = $db->sql_fetchrow($res);
			$row['current'] = (float) $amount;
		}else{
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_WRONG_ID.'<div>';
			CloseTable();
			include_once NUKE_BASE_DIR.'footer.php';
			exit;
		}
	}else{
		$res = $db->sql_query('SELECT `title`, `description`, `target`, `current` FROM `'.$prefix.'_donations_events` WHERE `active`=1 AND `id`='.$id);
		if ($db->sql_numrows($res)==0){
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_WRONG_ID.'<div>';
			CloseTable();
			include_once NUKE_BASE_DIR.'footer.php';
			exit;
		}
		$row = $db->sql_fetchrow();
	}
	
	OpenTable();
	echo '<div style="font-size:16px;font-weight:bold;text-align:center;">';
	echo $row['title'];
	echo '</div>';
	if (!empty($row['description'])){
		echo '<div style="font-size:12px;font-weight:bold;text-align:center;">';
		echo $row['description'];
		echo '</div>';
	}
	echo '<table style="width:100%;border-spacing:0;">';
	echo '<tr><td colspan="2" style="text-align:center;">';
	if ($row['target']!=0){
		echo '<br />';
		donation_meter($row['current'], $row['target'], $currency_symbol.currency($row['current']).' '.$currency.' / '.$currency_symbol.currency($row['target']).' '.$currency, 14, '50%', 18, $meter_border, $meter_background, $meter_text);
		echo '<br />';
	}
	echo '</td>';
	echo '<tr><td style="width:50%;vertical-align:top;">';
	
	echo '<script type="text/javascript">';
	echo '	var user_id = '.$userinfo['user_id'].';';
	echo '	function toggle_custom_value(){';
	echo '		if (nuke_jq(\'#amount\').val()==-1){';
	echo '			nuke_jq(\'#custom_amount_row\').show();';
	echo '		}else{';
	echo '			nuke_jq(\'#custom_amount_row\').hide();';
	echo '		}';
	echo '	}';
	echo '	nuke_jq(\'body\').delegate(\'#donation_submit\', \'click\', function(e) {';
	echo '		e.preventDefault();';
	echo '		var error = false;';
	echo '		if (nuke_jq(\'#amount\').val()==-1){';
	echo '			var amount = nuke_jq(\'#custom_donation_amount\').val();';
	echo '			if (isNaN(amount)||amount<=0){';
	echo '				error = true;';
	echo '				nuke_jq(\'#custom_value_error\').show();';
	echo '			}else{';
	echo '				nuke_jq(\'#custom_value_error\').hide();';
	echo '				nuke_jq(\'#donation_value\').val(amount);';
	echo '			}';
	echo '		}else{';
	echo '			nuke_jq(\'#donation_value\').val(nuke_jq(\'#amount\').val());';
	echo '		}';
	echo '		if (user_id!=1){';
	echo '			if (nuke_jq(\'#show_username\').val()==1){';
	echo '				nuke_jq(\'#user_id\').val(user_id);';
	echo '			}else{';
	echo '				nuke_jq(\'#user_id\').val(0);';
	echo '			}';
	echo '		}';
	echo '		if (error==false){';
	echo '			nuke_jq(\'#donations_form\').get(0).submit();';
	echo '		}';
	echo '	});';
	echo '</script>';
	
	global $sitename;
	$itemname = _DONATIONS_TO.' '.$sitename;
	if ($id!=0){
		$itemname .= ' '._DONATIONS_FOR.' '.$row['title'];
	}
	
	echo '<form id="donations_form" action="https://www.'.(($sandbox==1) ? 'sandbox.' : '').'paypal.com/cgi-bin/webscr" method="post" onsubmit="return false;">';
	echo '<input type="hidden" name="cmd" value="_xclick" />';
	echo '<input type="hidden" name="business" value="'.$receiver_email.'" />';
	echo '<input type="hidden" name="item_name" value="'.$itemname.'" />';
	echo '<input type="hidden" name="item_number" value="'.$id.'" />';
	echo '<input type="hidden" name="no_shipping" value="1" />';
	echo '<input type="hidden" name="notify_url" value="'.$ipn_url.'" />';
	echo '<input type="hidden" name="cancel_return" value="'.$cancel_url.'" />';
	echo '<input type="hidden" name="return" value="'.$return_url.'" />';
	echo '<input type="hidden" name="rm" value="2" />';
	echo '<input type="hidden" name="amount" id="donation_value" value="5"/>';
	echo '<input type="hidden" name="custom" id="user_id" value="'.(($userinfo['user_id']!=1) ? $userinfo['user_id'] : '0').'" />';
	
	echo '<table style="width:80%;border-spacing:0;margin: 0 auto;">';
	echo '<tr><th colspan="2" class="row1">'._DONATION_DETAILS.'</th></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;">'._DONATIONS_TOWARDS.'</td>';
	echo '<td class="row1" style="width:60%;font-weight:bold;padding-left:5px;">'.(($id==0) ? _DONATIONS_GENERAL_DONATION : $row['title']).'</td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;">'._DONATIONS_AMOUNT.'</td>';
	echo '<td class="row1" style="width:60%;font-weight:bold;padding-left:5px;">';
	echo '<select id="amount" onchange="toggle_custom_value();">';
	$preset_amounts = explode(',', $preset_amounts);
	foreach($preset_amounts as $p){
		echo '<option value="'.$p.'">'.currency($p).'</option>';
	}
	echo '<option value="-1">'._DONATIONS_CUSTOM_VALUE.'</option></select>';
	echo '';
	echo '<select name="currency_code">';
	$res = $db->sql_query('SELECT * FROM `'.$prefix.'_donations_currency`');
	while ($row=$db->sql_fetchrow($res)){
		echo '<option value="'.$row['currency'].'"'.(($row['currency']==$currency) ? ' selected="selected"' : '').'>'.$row['currency_symbol'].' '.$row['currency'].'</option>';
	}
	echo '</select>';
	echo '<tr id="custom_amount_row" style="display:none;"><td class="row1" style="width:40%;text-align:right;vertical-align:top;">'._DONATIONS_CUSTOM_VALUE.'</td>';
	echo '<td class="row1" style="width:60%;font-weight:bold;padding-left:5px;"><input type="text" id="custom_donation_amount" value="5" /><span id="custom_value_error" style="display:none;"><br />'._DONATIONS_CUSTOM_VALUE_ERROR.'</span></td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;">'._DONATIONS_PAYPAL_LANG.'</td>';
	echo '<td class="row1" style="width:60%;font-weight:bold;padding-left:5px;">';
	echo '<select name="lc">';
	$paypal_languages = explode(',', $paypal_languages);
	foreach($paypal_languages as $p){
		list($code, $language) = explode('|', $p);
		echo '<option value="'.trim($code).'">'.trim($language).'</option>';
	}
	echo '</select>';
	echo '</td></tr>';
	echo '<tr><td class="row1" style="width:40%;text-align:right;vertical-align:top;">'._DONATIONS_SHOW_USERNAME.'</td>';
	echo '<td class="row1" style="width:60%;font-weight:bold;padding-left:5px;">';
	if ($userinfo['user_id']!=1){
		echo '<select id="show_username">';
		echo '<option value="1">'._DONATIONS_SHOW_USERNAME_YES.'</option>';
		echo '<option value="0">'._DONATIONS_SHOW_USERNAME_NO.'</option>';
		echo '</select>';
	}else{
		echo _DONATIONS_SHOW_USERNAME_ANON;
	}
	echo '</td></tr>';
	echo '<tr><td class="row1" colspan="2" style="text-align:center;">';
	echo '<a href="#" id="donation_submit"><img style="width:110px;height:23px;border:0;" src="modules/'.$module_name.'/img/btn_donate_small.gif" title="Donate" alt="Donate" /></a>';
	echo '</td></tr>';
	echo '</table>';
	echo '</form>';
	
	echo '</td><td style="width:50%;vertical-align:top;">';
	
	echo '<table style="width:80%;border-spacing:0;margin:0 auto;">';
	echo '<tr><th class="row1" colspan="3">'._DONATIONS_DONATORS.'</th></tr>';
	
	$res = $db->sql_query('SELECT `user_id`, `mc_gross`, `mc_currency`, `currency_symbol`, `settle_amount` FROM `'.$prefix.'_donations` WHERE `event_id`=\''.$id.'\' ORDER BY `payment_date` DESC LIMIT 0,'.$donations_per_page);
	if ($db->sql_numrows($res)>0){
		$donators = array();
		$donations = array();
		while($row = $db->sql_fetchrow($res)){
			$donators[] = $row['user_id'];
			$donations[] = $row;
		}
		
		if (!empty($donators)){
			$in = implode(',', array_unique($donators));
			$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id` IN ('.$in.')');
			$donators = array();
			while($row = $db->sql_fetchrow($res)){
				$donators[$row['user_id']] = $row['username'];
			}
		}		
		
		foreach($donations as $donation){
			echo '<tr>';
			echo '<td class="row1" style="width:50%;text-align:center;font-weight:bold;">';
			echo (($donation['user_id']!=0) ? '<a href="modules.php?name=Profile&mode=viewprofile&u='.$donation['user_id'].'">'.UsernameColor($donators[$donation['user_id']]).'</a>' : _ANONYMOUS);
			echo '</td>';
			echo '<td class="row1" style="width:25%;text-align:center;font-weight:bold;">'.$donation['currency_symbol'].currency($donation['mc_gross']).' '.$donation['mc_currency'].'</td>';
			echo '<td class="row1" style="width:25%;text-align:center;font-weight:bold;">'.$currency_symbol.currency($donation['settle_amount']).' '.$currency.'</td>';
			echo '</tr>';
		}
	}else{
		echo '<tr><td class="row1" colspan="3" style="text-align:center;font-weight:bold;">'._DONATIONS_NO_DONATIONS.'</td></tr>';
	}
	echo '</table>';
	
	echo '</td></tr></table>';
	
	CloseTable();
}