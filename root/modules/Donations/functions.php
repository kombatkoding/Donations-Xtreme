<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : functions.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('NUKE_EVO')){
	die ("You can't access this file directly...");
}

function ipn_error( $content, $title = _DONATIONS_ERRORS_DEFAULT_IPN){
	$date = date("d M Y - H:i:s");
    $header = "---------[" . $title . "]------------------------------------------------------------------------------------------------------------\n";
    $wdata = $header;
    $wdata .= "- [" . $date . "] - \n";
    $wdata .= "\n";
    $wdata .= htmlspecialchars($content) . "\n";
    $wdata .= str_repeat('-', strlen($header));
    $wdata .= "\n\n";
    if($handle = @fopen(NUKE_INCLUDE_DIR.'log/error.log','a')) {
        fwrite($handle, $wdata);
        fclose($handle);
    }
}

function user_donation_stats_link($user){
	global $nukeurl;
	return $nukeurl.'modules.php?name=Profile&mode=viewprofile&u='.$user;
}

function donation_transaction_link($txn_id){
	global $nukeurl, $admin_file, $db, $prefix;
	$res = $db->sql_query('SELECT `id` FROM `'.$prefix.'_donations`, `'.$prefix.'_donations_archive` WHERE `txn_id`=\''.$txn_id.'\' AND `payment_status`=\'Completed\'');
	list($id) = $db->sql_fetchrow($res);
	return $nukeurl.$admin_file.'.php?op=Donations_View&id='.$id;
}

function currency($number){
	return number_format($number,2,'.','');
}

function donation_meter($amount, $goal, $text, $size, $width, $height, $border, $background, $textcolour, $return=false){
	if ($goal!=0){
		$percent = round($amount/$goal*100);
		if ($percent>100){
			$percent = 100;
		}
		$meter = '<div style="width:'.$width.';height:'.$height.'px;border:1px solid '.$bordercolour.';margin: 2px auto;overflow:hidden;">';
		$meter .= '<div style="width:'.$percent.'%;height:'.($height-1).'px;margin:1px;background-color:'.$background.';"></div>';
		$meter .= '<div style="width:100%;margin-top:-'.$height.'px;font-size:'.$size.'px;font-weight:bold;text-align:center;color:'.$textcolour.';">'.((empty($text)) ? $percent.' %' : $text).'</div>';
		$meter .= '</div>';
		if ($return){
			return $meter;
		}else{
			echo $meter;
		}
	}
}

function check_events(){
	global $db, $prefix;
		
	$res = $db->sql_query('SELECT `id`, `title`, `current`, `event_length`, `date_end`, `event_recurring` FROM `'.$prefix.'_donations_events` WHERE `time_based`=1 AND `active`=1 AND `date_end`<UNIX_TIMESTAMP()');
	if ($db->sql_numrows($res)>0){
		$res2 = $db->sql_query('SELECT currency.* FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
		list($currency, $currency_symbol) = $db->sql_fetchrow($res2);
		$events = array();
		while ($row = $db->sql_fetchrow($res)){
			if ($row['event_recurring']==1){
				list($years,$months,$weeks,$days) = explode(',', $row['event_length']);
				$plus = '';
				if ($years>0){
					$plus .= $years.' year'.(($years>1) ? 's ' : ' ');
				}
				if ($months>0){
					$plus .= $months.' month'.(($months>1) ? 's ' : ' ');
				}
				if ($weeks>0){
					$plus .= $weeks.' week'.(($weeks>1) ? 's ' : ' ');
				}
				if ($days>0){
					$plus .= $days.' day'.(($days>1) ? 's' : '');
				}
				$plus = trim($plus);
				$date_end = strtotime('+'.$plus,$row['date_end']);
				$data = "`date_end`='$date_end', `current`='0'";
			}else{
				$data = "`active`='0', `current`='0'";
			}
			if ($db->sql_query('UPDATE `'.$prefix.'_donations_events` SET '.$data.' WHERE `id`=\''.$row['id'].'\'')){
				$events[] = $row['id'];
				global $adminmail;
				$msg = _DONATIONS_EVENT_COMPLETE_MSG.' '.$currency_symbol.$row['current'].' '.$currency.'.';
				if ($row['event_recurring']){
					$msg .= _DONATIONS_EVENT_RESET.' - '.date('Y-m-d',$date_end).'.';
				}else{
					$msg .= _DONATIONS_EVENT_DEACTIVATED;
				}
			}
		}
		
		if (!empty($events)){
			$in = implode(',',$events);
			if ($db->sql_query('INSERT INTO `'.$prefix.'_donations_archive` SELECT `id`, `user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations` WHERE `event_id` IN ('.$in.')')){
				$db->sql_query('DELETE FROM `'.$prefix.'_donations` WHERE `event_id` IN ('.$in.')');
			}
		}
		
	}
	
}