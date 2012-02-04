<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : main.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('MODULE_FILE')){
	die ("You can't access this file directly...");
}

global $db, $prefix;

$res = $db->sql_query('SELECT `site_active` FROM `'.$prefix.'_donations_config`');
list($site_donations_active) = $db->sql_fetchrow($res);
if ($site_donations_active){
	OpenTable();
	echo '<div style="font-size:16px;font-weight:bold;text-align:center;">';
	echo _DONATIONS_FEELING_GENEROUS.'<br /><br />';
	echo '<a href="modules.php?name='.$module_name.'&op=Donate&id=0"><img style="width:122px;height:47px;border:0;" src="modules/'.$module_name.'/img/btn_donate_large.gif" title="'._DONATIONS_DONATE.'" alt="'._DONATIONS_DONATE.'" /></a>';
	echo '</div>';
	CloseTable();
	echo '<br />';
}


$res = $db->sql_query('SELECT currency.*, config.`site_active`, config.`site_target`, config.`meter_border`, config.`meter_background`, config.`meter_text`, config.`donations_per_page` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
list($currency, $currency_symbol, $site_active, $site_target, $meter_border, $meter_background, $meter_text, $donations_per_page) = $db->sql_fetchrow($res);

$events = (($site_active) ? array(0 => array('title' => _DONATIONS_SITE, 'target'=> $site_target, 'donations' => 0, 'amount' => 0)) : array());
$res = $db->sql_query('SELECT `id`, `title`, `target`, `current` FROM `'.$prefix.'_donations_events` WHERE `active`=1');
while($row = $db->sql_fetchrow($res)){
	$events[$row['id']] = array('title' => $row['title'], 'target'=> $row['target'], 'donations' => 0, 'amount' => $row['current']);
}

$res = $db->sql_query('SELECT `user_id`, `mc_gross`, `mc_currency`, `currency_symbol`, `settle_amount`, `event_id` FROM `'.$prefix.'_donations` ORDER BY `payment_date` DESC');
$donators = array();
$donations = array();
while($row = $db->sql_fetchrow($res)){
	$donations[] = $row;
	$donators[] = $row['user_id'];
	if ($row['event_id']==0){
		if ($site_active){
			$events[0]['amount'] += $row['settle_amount'];
			$events[0]['donations']++;
		}
	}else{
		$events[$row['event_id']]['donations']++;
	}
}

if (!empty($donators)){
	$in = implode(',', array_unique($donators));
	$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id` IN ('.$in.')');
	$donators = array();
	while($row = $db->sql_fetchrow($res)){
		$donators[$row['user_id']] = $row['username'];
	}
}

OpenTable();
echo '<table style="width:100%;border-spacing:0;">';
echo '<tr><td style="50%;vertical-align:top;">';

if (!empty($events)){
	foreach($events as $id=>$event){
		echo '<table style="width:80%;border-spacing:0;margin:0 auto;">';
		echo '<tr><th class="row1" colspan="2">'.$event['title'].'</th></tr>';
		echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_DONATIONS.'</td>';
		echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$event['donations'].'</td></tr>';
		echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_CURRENT_AMOUNT.'</td>';
		echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($event['amount']).' '.$currency.'</td></tr>';
		if ($event['target']!=0){
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_GOAL.'</td>';
			echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($event['target']).' '.$currency.'</td></tr>';
			echo '<tr><td colspan="2" class="row1">';
			donation_meter($event['amount'], $event['target'], '', 14, '80%', 18, $meter_border, $meter_background, $meter_text);
			echo '</td></tr>';
		}
		echo '<tr><td class="row1" colspan="2" style="text-align:center;">';
		echo '<a href="modules.php?name='.$module_name.'&op=Donate&id='.$id.'"><img style="width:110px;height:23px;border:0;" src="modules/'.$module_name.'/img/btn_donate_small.gif" title="'._DONATIONS_DONATE.'" alt="'._DONATIONS_DONATE.'" /></a>';
		echo '</td></tr>';
		echo '</table><br />';
	}
}else{
	echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_NONE_ACTIVE.'</div>';
}
echo '</td><td style="width:50%;vertical-align:top;">';
echo '<script type="text/javascript">';
echo 'var page=1;';
echo 'var total_pages='.ceil(count($donations)/$donations_per_page).';';
echo '</script>';


echo '<table style="width:80%;border-spacing:0;margin:0 auto;">';
echo '<tr><th class="row1" colspan="3">'._DONATIONS_DONATORS.'</th></tr>';
echo '<tr><td class="row1" colspan="3"><span style="float:left;padding-left:10px;"><a id="donations_previous" href="javascript:void();" onclick="donations_change_page(page-1);return false;" style="font-weight:bold;display:none;"><< Previous</span><span style="float:right;padding-right:10px;"><a id="donations_next" href="javascript:void();" onclick="donations_change_page(page+1);return false;" style="font-weight:bold;'.((ceil(count($donations)/$donations_per_page)>1) ? '' : 'display:none;').'">Next >></a></span></th></tr>';
$i = 0;
foreach($donations as $donation){
	$i++;
	$page = ceil($i/$donations_per_page);
	echo '<tr class="donations_page_'.$page.'"'.(($page>1) ? ' style="display:none;"' : '').'>';
	echo '<td class="row1" style="width:50%;text-align:center;font-weight:bold;">';
	echo (($donation['user_id']!=0) ? '<a href="modules.php?name=Profile&mode=viewprofile&u='.$donation['user_id'].'">'.UsernameColor($donators[$donation['user_id']]).'</a>' : _ANONYMOUS);
	echo '</td>';
	echo '<td class="row1" style="width:25%;text-align:center;font-weight:bold;">'.$donation['currency_symbol'].currency($donation['mc_gross']).' '.$donation['mc_currency'].'</td>';
	echo '<td class="row1" style="width:25%;text-align:center;font-weight:bold;">'.$currency_symbol.currency($donation['settle_amount']).' '.$currency.'</td>';
	echo '</tr>';
}

echo '</table>';
echo '<script type="text/javascript">';
echo 'function donations_change_page(p){';

echo '	nuke_jq(\'.donations_page_\'+page).fadeOut(\'slow\', function(){';
echo '		nuke_jq(\'.donations_page_\'+p).fadeIn(\'slow\');';
echo '		if (p==1){';
echo '			nuke_jq(\'#donations_previous\').hide();';
echo '		}else{';
echo '			nuke_jq(\'#donations_previous\').show();';
echo '		}';
echo '		if (p==total_pages){';
echo '			nuke_jq(\'#donations_next\').hide();';
echo '	}else{';
echo '			nuke_jq(\'#donations_next\').show();';
echo '		}';
echo '		page=p;';
echo '	});';

echo '}';
echo '</script>';

echo '</td></tr></table>';
CloseTable();