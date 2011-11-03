<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : index.php
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
include_once NUKE_BASE_DIR.'header.php';

if (is_mod_admin($module_name)){
    global $admin_file;
    OpenTable();
	echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_ADMIN_PANEL.'</div><br /><div style="text-align:center;">';
	echo '<a href="'.$admin_file.'.php?op=Donations">'._DONATIONS_ADMIN_HOME.'</a> | ';
	echo '<a href="'.$admin_file.'.php?op=Donations_Config">'._DONATIONS_CONFIG.'</a> | ';
	echo '<a href="'.$admin_file.'.php?op=Donations_View">'._DONATIONS_VIEW.'</a> | ';
	echo '<a href="'.$admin_file.'.php?op=Donations_Events">'._DONATIONS_EVENTS.'</a> | ';
	echo '<a href="'.$admin_file.'.php?op=Donations_Blocks">'._DONATIONS_BLOCKS_MANAGE.'</a> | ';
	echo '<a href="'.$admin_file.'.php">'._DONATIONS_RETURNMAIN.'</a>';
	echo '</div>';
	CloseTable();
	echo '<br />';
	    
	switch($op){
		case 'Donations_Blocks': include_once (NUKE_MODULES_DIR.$module_name.'/admin/blocks.php'); break;
		case 'Donations_Config': include_once (NUKE_MODULES_DIR.$module_name.'/admin/config.php'); break;
		case 'Donations_Events': include_once (NUKE_MODULES_DIR.$module_name.'/admin/events.php'); break;
		case 'Donations_View': include_once (NUKE_MODULES_DIR.$module_name.'/admin/view.php'); break;
		case 'Donations':
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS.'</div><br />';
			echo '<table style="width:100%;"><tr><td style="width:50%;vertical-align:top;">';
			
			$res = $db->sql_query('SELECT currency.* FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
			list($currency, $currency_symbol) = $db->sql_fetchrow($res);
			
			$res = $db->sql_query('SELECT `site_active`, `site_target` FROM `'.$prefix.'_donations_config`');
			list($site_donations_active, $site_target) = $db->sql_fetchrow($res);
			
			$res = $db->sql_query('SELECT `user_id`, `first_name`, `last_name`, `settle_amount`, `event_id`, `payment_date` FROM `'.$prefix.'_donations` UNION SELECT `user_id`, `first_name`, `last_name`, `settle_amount`, `event_id`, `payment_date` FROM `'.$prefix.'_donations_archive`');
			$donators = array();
			$stats['week'] = array('number'=>0, 'amount'=>0);
			$stats['month'] = array('number'=>0, 'amount'=>0);
			$stats['sixmonths'] = array('number'=>0, 'amount'=>0);
			$stats['year'] = array('number'=>0, 'amount'=>0);
			$stats['life'] = array('number'=>0, 'amount'=>0);
			$site_amount = 0;
			while ($row = $db->sql_fetchrow($res)){
				if ($row['event_id']==0){
					$site_amount += $row['settle_amount'];
				}
				if ($row['user_id']!=0){
					if (isset($donators[$row['user_id']])){
						$donators[$row['user_id']] += $row['settle_amount'];
					}else{
						$donators[$row['user_id']] = $row['settle_amount'];
					}
				}
				$time = time();
				$stats['life']['number'] += 1;
				$stats['life']['amount'] += $row['settle_amount'];
				if ($row['payment_date']>($time-31556926)){
					$stats['year']['number'] += 1;
					$stats['year']['amount'] += $row['settle_amount'];
					if ($row['payment_date']>($time-15778463)){
						$stats['sixmonths']['number'] += 1;
						$stats['sixmonths']['amount'] += $row['settle_amount'];
						if ($row['payment_date']>($time-2629743)){
							$stats['month']['number'] += 1;
							$stats['month']['amount'] += $row['settle_amount'];
							if ($row['payment_date']>($time-604800)){
								$stats['week']['number'] += 1;
								$stats['week']['amount'] += $row['settle_amount'];
							}
						}
					}
				}
			}
			arsort($donators);
			$i=1;
			foreach($donators as $id=>$amount){
				if ($i>5){
					unset($donators[$id]);
				}
				$i++;
			}
			if (!empty($donators)){
				$in = implode(',', array_keys($donators));
				$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id` IN ('.$in.')');
				$top5 = array();
				while($row = $db->sql_fetchrow($res)){
					$top5[$row['user_id']] = array('username'=>$row['username'], 'amount'=> $donators[$row['user_id']]);
				}
			}
			
			echo '<table style="width:80%;border-spacing:0;margin:0 auto;"><tr><th colspan="3">'._DONATIONS_GLOBAL_STATS.'</th></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_LAST_WEEK.'</td><td class="row2" style="width:20%;font-weight:bold;text-align:center;">'.$stats['week']['number'].'</td><td class="row2" style="width:40%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($stats['week']['amount']).' '.$currency.'</td></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_LAST_MONTH.'</td><td class="row2" style="width:20%;font-weight:bold;text-align:center;">'.$stats['month']['number'].'</td><td class="row2" style="width:40%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($stats['month']['amount']).' '.$currency.'</td></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_LAST_SIX_MONTHS.'</td><td class="row2" style="width:20%;font-weight:bold;text-align:center;">'.$stats['sixmonths']['number'].'</td><td class="row2" style="width:40%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($stats['sixmonths']['amount']).' '.$currency.'</td></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_LAST_YEAR.'</td><td class="row2" style="width:20%;font-weight:bold;text-align:center;">'.$stats['year']['number'].'</td><td class="row2" style="width:40%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($stats['year']['amount']).' '.$currency.'</td></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_LIFETIME.'</td><td class="row2" style="width:20%;font-weight:bold;text-align:center;">'.$stats['life']['number'].'</td><td class="row2" style="width:40%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($stats['life']['amount']).' '.$currency.'</td></tr>';
			echo '</table><br />';
			
			echo '<table style="width:80%;border-spacing:0;margin:0 auto;"><tr><th colspan="2">'._DONATIONS_TOP_FIVE_DONATORS.'</th></tr>';			
			foreach($donators as $key=>$val){
				$user = $top5[$key];
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;"><a href="modules.php?name=Profile&mode=viewprofile&u='.$key.'">'.UsernameColor($user['username']).'</a></td><td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($user['amount']).' '.$currency.'</td></tr>';
			}
			
			
			echo '</table><br />';
			
			echo '</td><td style="width:50%;vertical-align:top;">';
			
			$res = $db->sql_query('SELECT `event_id`, COUNT(`id`) as c FROM `nuke_donations` GROUP BY `event_id`');
			while ($row = $db->sql_fetchrow($res)){
				$event_count[$row['event_id']] = $row['c'];
			}
			
			echo '<table style="width:80%;border-spacing:0;margin:0 auto;">';
			echo '<tr><th class="row1" colspan="2">'._DONATIONS_SITE.(($site_donations_active) ? '' : ' ('._DONATIONS_INACTIVE.')').'</th></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_DONATIONS.'</td>';
			echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.((isset($event_count[0])) ? $event_count[0] : 0).'</td></tr>';
			echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_CURRENT_AMOUNT.'</td>';
			echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($site_amount).' '.$currency.'</td></tr>';
			if ($site_target>0){
				$difference = $site_amount-$site_target;
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_GOAL.'</td>';
				echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($site_target).' '.$currency.'</td></tr>';
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'.(($difference>0) ? _DONATIONS_ABOVE_GOAL : _DONATIONS_BELOW_GOAL).'</td>';
				echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency(abs($difference)).' '.$currency.'</td></tr>';
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_PERCENT_COMPLETE.'</td>';
				echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.round($site_amount/$site_target*100).'%</td></tr>';
			}
			echo '</table><br />';
			
			$res = $db->sql_query('SELECT `id`, `title`, `time_based`, `date_end`, `target`, `current`, `active` FROM `'.$prefix.'_donations_events`');
			while ($row = $db->sql_fetchrow($res)){
				echo '<table style="width:80%;border-spacing:0;margin:0 auto;">';
				echo '<tr><th class="row1" colspan="2">'.$row['title'].(($row['active']) ? '' : ' ('._DONATIONS_INACTIVE.')').'</th></tr>';
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_DONATIONS.'</td>';
				echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.((isset($event_count[$row['id']])) ? $event_count[$row['id']] : 0).'</td></tr>';
				echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_CURRENT_AMOUNT.'</td>';
				echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($row['current']).' '.$currency.'</td></tr>';
				if ($row['target']>0){
					echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_GOAL.'</td>';
					echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency($row['target']).' '.$currency.'</td></tr>';
					$difference = $row['current']-$row['target'];
					echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'.(($difference>0) ? _DONATIONS_ABOVE_GOAL : _DONATIONS_BELOW_GOAL).'</td>';
					echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.$currency_symbol.currency(abs($difference)).' '.$currency.'</td></tr>';
					echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_PERCENT_COMPLETE.'</td>';
					echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.round($row['current']/$row['target']*100).'%</td></tr>';
				}
				if ($row['time_based']==1){
					echo '<tr><td class="row1" style="width:40%;text-align:right;padding-right:5px;">'._DONATIONS_END_DATE.'</td>';
					echo '<td class="row2" style="width:60%;font-weight:bold;padding-left:5px;">'.date('d/m/Y', $row['date_end']).'</td></tr>';
				}
				echo '</table><br />';
			}
			
			
			echo '</td></tr></table>';			
			CloseTable();
		break;
	}
}else{
    OpenTable();
	echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_ERROR.'<br />'._DONATIONS_NO_PERM.'</div>';
	CloseTable();
}

include_once NUKE_BASE_DIR.'footer.php';

?>