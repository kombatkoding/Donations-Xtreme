<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : block.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/


if (preg_match('/block-Donations_/i',$_SERVER['PHP_SELF'])) {
    header('Location: ../index.php');
    die();
}
global $db, $prefix, $currentlang;
include_once NUKE_MODULES_DIR.$data['module'].'/language/lang-'.$currentlang.'.php';
include_once NUKE_MODULES_DIR.$data['module'].'/functions.php';


$res = $db->sql_query('SELECT currency.*, config.`site_target`, config.`meter_border`, config.`meter_background`, config.`meter_text`, config.`donations_per_page`, config.`preset_amounts`, config.`paypal_languages`, config.`ipn_url`, config.`return_url`, config.`cancel_url`, config.`site_active` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
list($currency, $currency_symbol, $site_target, $meter_border, $meter_background, $meter_text, $donations_per_page, $preset_amounts, $paypal_languages, $ipn_url, $return_url, $cancel_url, $site_donations_active) = $db->sql_fetchrow($res);

if ($data['id']==-1){
	$active = array();
	if ($site_donations_active==1){
		$active[] = 0;
	}
	$res = $db->sql_query('SELECT `id` FROM `'.$prefix.'_donations_events` WHERE `active`=1');
	while(list($i) = $db->sql_fetchrow($res)){
		$active[] = $i;
	}
	$data['id'] = $active[array_rand($active)];
}

$content = '';

if ($data['id']==0){
	$event = array('title' => _DONATIONS_SITE, 'target' => $site_target, 'date_end' => 0, 'description' => '');
	$res = $db->sql_query('SELECT SUM(`mc_gross`*`exchange_rate`) AS total, SUM(`mc_fee`*`exchange_rate`)AS fees, SUM(`settle_amount`) FROM `'.$prefix.'_donations` WHERE `event_id`='.$data['id']);
	list($total, $fees, $sum) = $db->sql_fetchrow($res);
	$event['total'] = $total;
	$event['fees'] = $fees;
	$event['current'] = $sum;
}else{
	$res = $db->sql_query('SELECT `title`, `target`, `current`, `date_end`, `description` FROM `'.$prefix.'_donations_events` WHERE `id`='.$data['id']);
	$event = $db->sql_fetchrow();
	if ($data['show_total_donated'] || $data['show_total_fees']){
		$res = $db->sql_query('SELECT SUM(`mc_gross`*`exchange_rate`) AS total, SUM(`mc_fee`*`exchange_rate`)AS fees, SUM(`settle_amount`) FROM `'.$prefix.'_donations` WHERE `event_id`='.$data['id']);
		list($total, $fees) = $db->sql_fetchrow($res);
		$event['total'] = $total;
		$event['fees'] = $fees;
	}
}
if ($data['donators_to_show'] != 0){
	$res = $db->sql_query('SELECT `user_id`, `settle_amount`, `payment_date`, `event_id` FROM `'.$prefix.'_donations` WHERE `event_id`='.$data['id'].' ORDER BY `payment_date` DESC'.(($donators_to_show != -1) ? ' LIMIT 0,'.((int) $data['donators_to_show']) : ''));
	$donations = array();
	$donators = array();
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
}

$content .= '';
$content .= '<div style="width:95%;margin:0 auto;text-align:center;">';
$content .= '<div style="width:100%;font-weight:bold;">'.$event['title'].'</div>';
if ($data['show_desc'] && !empty($event['description'])){
	$content .= '<div style="width:100%;">'.$event['description'].'</div>';
}
$content .= '<br />';
if ($data['bar_position']=='top'){
	$content .= donation_meter($event['current'], $event['target'], '', 14, '100%', 18, $meter_border, $meter_background, $meter_text, 1).'<br />';
}
$content .= '<div style="text-align:center;">';
$content .= '<a href="modules.php?name='.$data['module'];
$content .= (($data['donate_page']) ? '&op=Donate&id='.$data['id'] : '');
$content .= '"><img style="width:110px;height:23px;border:0;" src="modules/'.$data['module'].'/img/btn_donate_small.gif" title="Donate" alt="Donate" /></a>';
$content .= '</div>';
$content .= '<br />';
if ($data['bar_position']=='middle'){
	$content .= donation_meter($event['current'], $event['target'], '', 14, '100%', 18, $meter_border, $meter_background, $meter_text, 1);
}

$content .= '<table style="width:100%;border-spacing:0;">';
if ($data['show_end_date'] && $event['date_end']!=0){
	$content .= '<tr><td style="width:65%;text-align:left;">'._DONATIONS_END_DATE.'</td>';
	$content .= '<td style="width:35%;text-align:right;">'.date('M d', $event['date_end']).'</td></tr>';
}
if ($data['show_total_donated']){
	$content .= '<tr><td style="width:65%;text-align:left;">'._DONATIONS_TOTAL.'</td>';
	$content .= '<td style="width:35%;text-align:right;">'.$currency_symbol.currency((empty($event['total'])) ? 0 : $event['total']).'</td></tr>';
}
if ($data['show_total_fees']){
	$content .= '<tr><td style="width:65%;text-align:left;">'._DONATIONS_FEES.'</td>';
	$content .= '<td style="width:35%;text-align:right;">'.$currency_symbol.currency((empty($event['fees'])) ? 0 : $event['fees']).'</td></tr>';
}
if ($data['show_net_donations']){
	$content .= '<tr><td style="width:65%;text-align:left;">'._DONATIONS_NET_AMOUNT.'</td>';
	$content .= '<td style="width:35%;text-align:right;">'.$currency_symbol.currency((empty($event['current'])) ? 0 : $event['current']).'</td></tr>';
}
if ($event['target']>0){
	if ($data['show_target']){
		$content .= '<tr><td style="width:65%;text-align:left;">'._DONATIONS_GOAL.'</td>';
		$content .= '<td style="width:35%;text-align:right;">'.$currency_symbol.currency($event['target']).'</td></tr>';
	}
	if ($data['show_below_goal']){
		$difference = $event['current']-$event['target'];
		$content .= '<tr><td style="width:65%;text-align:left;">'.(($difference>0) ? _DONATIONS_ABOVE_GOAL : _DONATIONS_BELOW_GOAL).'</td>';
		$content .= '<td style="width:35%;text-align:right;">'.$currency_symbol.currency(abs($difference)).'</td></tr>';
	}
}
if ($data['show_currency']){
	$content .= '<tr><td style="width:70%;text-align:left;">'._DONATIONS_CURRENCY.'</td>';
	$content .= '<td style="width:30%;text-align:right;">'.$currency.'</td></tr>';
}
$content .= '</table><br />';
if ($data['bar_position']=='bottom'){
	$content .= donation_meter($event['current'], $event['target'], '', 14, '100%', 18, $meter_border, $meter_background, $meter_text, 1);
}
if ($data['donators_to_show']!=0){
	$content .= '<table style="width:100%;border-spacing:0;">';
	$content .= '<tr><td colspan="3" style="text-align:center;font-weight:bold;">'._DONATIONS_DONATORS.'</td></tr>';
	foreach($donations as $d){
		$content .= '<tr>';
		if ($data['show_date']){
			$content .= '<td>'.date('M d', $d['payment_date']).'</td>';
		}
		$content .= '<td>';
		$content .= (($d['user_id']!=0) ? '<a href="modules.php?name=Profile&mode=viewprofile&u='.$d['user_id'].'">'.UsernameColor($donators[$d['user_id']]).'</a>' : _ANONYMOUS);
		$content .= '</td>';
		
		$content .= '<td style="text-align:right;">'.$currency_symbol.currency($d['settle_amount']).'</td>';
		$content .= '</tr>';
	}
	$content .= '</table>';
}

$content .= '</div>';
