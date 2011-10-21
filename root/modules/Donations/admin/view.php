<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : view.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

function list_donations(){
	global $db, $prefix, $admin_file, $_GETVAR;
	
	$res = $db->sql_query('SELECT currency.*, config.`site_active` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
	list($currency, $currency_symbol, $site_active) = $db->sql_fetchrow($res);
	
	if (isset($_POST['complete'])){
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
				$itemname = _DONATIONS_TO.' '.$sitename;
			}else{
				list($event_title) = $db->sql_fetchrow($res);
				global $sitename;
				$itemname = _DONATIONS_TO.' '.$sitename.' '._DONATIONS_FOR.' '.$row['title'];
			}
		}
		$time = time();
		$db->sql_query('INSERT INTO `'.$prefix."_donations` (`user_id`,`item_name`,`item_number`,`payment_status`,`payment_date`,`mc_gross`,`mc_fee`,`mc_currency`,`settle_amount`,`exchange_rate`,`first_name`,`last_name`,`payer_email`,`currency_symbol`,`event_id`) VALUES ('$user', '$item_name', '$event', 'Completed', '$time', '$amount','0','$currency','$amount','1','$first_name','$last_name','$email','$currency_symbol','$event')");
		$db->sql_query('UPDATE `'.$prefix.'_donations_events` SET `current`=`current`+'.$amount.' WHERE `id`='.$event);
		
	}
	
	$sort_value = false;
	$order = '`payment_date` DESC';
	$order_value= 'date';
	if (isset($_GET['sort'])){
		if(isset($_GET['order'])){
			$sort = ((bool) $_GET['order']) ? ' ASC' : ' DESC';
			$sort_value = (bool) $_GET['order'];
		}else{
			$sort = 'ASC';
		}
		$methods = array(
			'user'=>'`user_id`'.$sort,
			'date'=>'`payment_date`'.$sort,
			'status'=>'`payment_status`'.$sort,
			'amount'=>'`mc_gross`'.$sort,
			'settle_amount'=>'`settle_amount`.$sort',
			'currency'=>'`mc_currency`.$sort',
			'name'=>'`last_name`'.$sort.', `first_name`'.$sort,
			'event'=>'`event_id`'.$sort
		);
		if (in_array($_GET['sort'],array_keys($methods))){
			$order = $methods[$_GET['sort']];
			$order_value = $_GET['sort'];
		}
	}
	
	$user_filter = (isset($_GET['user']) ? (int) $_GET['user'] : 0);
	$where = ($user_filter!=0) ? ' AND `user_id`='.$user_filter : '';
	
	OpenTable();
	echo '<script type="text/javascript">';
	echo '	function donation_submit(){';
	echo '		var error = false;';
	echo '		var user = nuke_jq(\'#user_id\').val();';
	echo '		var amount = nuke_jq(\'#amount\').val();';
	echo '		if (isNaN(user)||user==""||user<0){';
	echo '			error = true;';
	echo '			nuke_jq(\'#user_error\').show();';
	echo '		}else{';
	echo '			nuke_jq(\'#user_error\').hide();';
	echo '			if (user==1){';
	echo '				nuke_jq(\'#user_id\').val(0);';
	echo '			}';
	echo '		}';
	echo '		if (isNaN(amount)||amount<=0){';
	echo '			error = true;';
	echo '			nuke_jq(\'#amount_error\').show();';
	echo '		}else{';
	echo '			nuke_jq(\'#amount_error\').hide();';
	echo '		}';
	echo '		if (error==false){';
	echo '			nuke_jq(\'#add_donation_form\').get(0).submit();';
	echo '		}';
	echo '	}';
	echo '</script>';
	echo '<div style="text-align:center;font-weight:bold;">';
	echo '<form action="'.$admin_file.'.php" method="get">';
	echo _DONATIONS_SORT_BY.'&nbsp;&nbsp;';
	echo '<input type="hidden" name="op" value="Donations_View" />';
	echo '<select name="sort">';
	echo '<option value="date"'.(($order_value=='date') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_DATE.'</option>';
	echo '<option value="user"'.(($order_value=='user') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_USER.'</option>';
	echo '<option value="status"'.(($order_value=='status') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_STATUS.'</option>';
	echo '<option value="name"'.(($order_value=='name') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_NAME.'</option>';
	echo '<option value="amount"'.(($order_value=='amount') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_AMOUNT.'</option>';
	echo '<option value="settle_amount"'.(($order_value=='settle_amount') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_SETTLE_AMOUNT.'</option>';
	echo '<option value="currency"'.(($order_value=='currency') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_CURRENCY.'</option>';
	echo '<option value="event"'.(($order_value=='event') ? ' selected="selected"' : '').'>'._DONATIONS_SORT_EVENT.'</option>';
	echo '</select> ';
	echo '<select name="order">';
	echo '<option value="1"'.(($sort_value) ? ' selected="selected"' : '').'>'._DONATIONS_SORT_ASC.'</option>';
	echo '<option value="0"'.(($sort_value) ? '' : ' selected="selected"').'>'._DONATIONS_SORT_DESC.'</option></select> ';
	echo '<input type="submit" value="'._DONATIONS_SORT_GO.'" />';
	echo '</form>';
	
	echo '<form action="'.$admin_file.'.php" method="get">';
	echo _DONATIONS_FILTER_USER.':'.'&nbsp;&nbsp;';
	echo '<input type="hidden" name="op" value="Donations_View" />';
	echo '<select name="user">';
	echo '<option value="0">No Filter</option>';
	$res = $db->sql_query('(SELECT `user_id` from `nuke_donations`) UNION (SELECT `user_id` from `nuke_donations_archive`)');	
	$users = array();
	while($row = $db->sql_fetchrow($res)){
		if ($row['user_id']==1){
			echo '<option value="1">'._ANONYMOUS.'</option>';
		}elseif ($row['user_id']!=0){
			$users[] = $row['user_id'];
		}
	}
	if (!empty($users)){
		$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id` IN ('.implode(',',$users).') ORDER BY `username`');
		while($row = $db->sql_fetchrow($res)){
			$users[$row['user_id']] = $row['username'];
			echo '<option value="'.$row['user_id'].'"'.(($user_filter==$row['user_id']) ? ' selected="selected"' : '').'>'.$row['username'].'</option>';
		}
	}
	echo '</select> ';
	echo '<input type="submit" value="'._DONATIONS_FILTER_GO.'" />';
	echo '</form>';
	
	echo '<a href="javascript:void();" onclick="nuke_jq(\'#new_donation\').slideToggle();return false;">'._DONATIONS_MANUALLY_ADD.'</a>';
	echo '<div id="new_donation" style="width:450px;margin:0 auto;text-align:left;display:none;">';
	echo '<form id="add_donation_form" action="'.$admin_file.'.php?op=Donations_View" method="post" onsubmit="return false;">';
	echo '<input type="hidden" name="complete" value="1" />';
	echo '<table style="width:100%;">';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_USER_ID.' *</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;">';
	echo '<select id="user_id" name="user_id">';
	$res = $db->sql_query('SELECT `user_id`,`username` FROM `'.$prefix.'_users`');
	while($row = $db->sql_fetchrow($res)){
		echo '<option value="'.$row['user_id'].'">'.$row['username'].'</option>';
	}
	echo '</select>';
	echo '</td></tr>';
	echo '<tr id="user_error" style="display:none;"><td class="row1" style="width:25%;text-align:right;">&nbsp;</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;">'._DONATIONS_USERID_MUST_BE_NUMBER.'</td></tr>';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_FIRST_NAME.'</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;"><input type="text" name="first_name" /></td></tr>';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_LAST_NAME.'</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;"><input type="text" name="last_name" /></td></tr>';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_EMAIL.'</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;"><input type="text" name="email" /></td></tr>';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_AMOUNT.' ('.$currency_symbol.') *</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;"><input type="text" id="amount" name="amount" /></td></tr>';
	echo '<tr id="amount_error" style="display:none;"><td class="row1" style="width:25%;text-align:right;">&nbsp;</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;">'._DONATIONS_AMOUNT_MUST_BE_NUMBER.'</td></tr>';
	echo '<tr><td class="row1" style="width:25%;text-align:right;">'._DONATIONS_EVENT.' *</td>';
	echo '<td class="row1" style="width:75%;padding-left:5px;font-weight:bold;">';
	echo '<select name="event">';
	if ($site_active){
		echo '<option value="0">'._DONATIONS_SITE.'</option>';
	}
	$res = $db->sql_query('SELECT `id`, `title` FROM `'.$prefix.'_donations_events` WHERE `active`=1');
	while ($row=$db->sql_fetchrow($res)){
		echo '<option value="'.$row['id'].'">'.$row['title'].'</option>';
	}
	echo '</select>';
	echo '<tr><td class="row1" colspan="2" style="width:25%;text-align:center;">* '._DONATIONS_REQUIRED_FIELDS.'</td></tr>';
	echo '<tr><td class="row1" colspan="2" style="width:25%;text-align:center;"><a href="#" onclick="donation_submit();return false;" style="font-weight:bold;">'._DONATIONS_ADD.'</button></td></tr>';	
	echo '</table>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	CloseTable();
	OpenTable();
	echo '<table style="width:100%;border-spacing:0;"><tr><th class="row1">'._DONATIONS_DONATOR.'</th><th class="row1">'._DONATIONS_STATUS.'</th><th class="row1">'._DONATIONS_AMOUNT.'</th><th class="row1">'._DONATIONS_SETTLE_AMOUNT.'</th><th class="row1">'._DONATIONS_DATE.'</th><th class="row1">'._DONATIONS_EVENT.'</th><th class="row1">'._DONATIONS_VIEW_LINK.'</th></tr>';
	
	$events[0] = _DONATIONS_SITE_DONATIONS;
	$res = $db->sql_query('SELECT `id`, `title` FROM `'.$prefix.'_donations_events`');
	while ($row = $db->sql_fetchrow($res)){
		$events[$row['id']] = $row['title'];
	}
	
	$res = $db->sql_query('(SELECT 0 AS `archived`, `id`, `user_id`, `payment_date`, `payment_status`, `mc_gross`, `mc_currency`, `settle_amount`, `first_name`, `last_name`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations` WHERE (`payment_status` = \'Completed\' OR `payment_status` = \'Refunded\')'.$where.') UNION (SELECT 1 AS `archived`, `id`, `user_id`, `payment_date`, `payment_status`, `mc_gross`, `mc_currency`, `settle_amount`, `first_name`, `last_name`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations_archive` WHERE (`payment_status` = \'Completed\' OR `payment_status` = \'Refunded\')'.$where.') ORDER BY '.$order);
	$donations = array();
	$donators = array();
	while($row = $db->sql_fetchrow($res)){
		$donations[] = $row;
		if ($row['user_id']!=0){
			$donators[$row['user_id']] = '';
		}
	}
	if (!empty($donators)){
		$res = $db->sql_query('SELECT `user_id`, `username` FROM `'.$prefix.'_users` WHERE `user_id` IN ('.implode(',',array_keys($donators)).')');
		while($row = $db->sql_fetchrow($res)){
			$donators[$row['user_id']] = $row['username'];
		}
	}
	global $ThemeInfo;
	foreach($donations as $d){
		echo '<tr'.(($d['archived']==1) ? ' style="color:'.$ThemeInfo['textcolor2'].';"' : '').'>';
		echo '<td class="row1" style="text-align:center;font-weight:bold;">';
		if ($d['user_id']!=0){
			echo '<a href="modules.php?name=Profile&mode=viewprofile&u='.$d['user_id'].'">'.UsernameColor($donators[$d['user_id']]).'</a>';
		}else{
			echo $d['first_name'].' '.$d['last_name'];
		}
		echo '</td>';
		echo '<td class="row1" style="text-align:center;">'.(($d['payment_status']=='Completed') ? _DONATIONS_STATUS_COMPLETED : _DONATIONS_STATUS_REFUNDED).'</td>';
		echo '<td class="row1" style="text-align:center;">'.$d['currency_symbol'].currency($d['mc_gross']).' '.$d['mc_currency'].'</td>';
		echo '<td class="row1" style="text-align:center;">'.$currency_symbol.currency($d['settle_amount']).' '.'</td>';
		echo '<td class="row1" style="text-align:center;">'.date('d/m/Y', $d['payment_date']).'</td>';
		echo '<td class="row1" style="text-align:center;">'.$events[$d['event_id']].'</td>';
		echo '<td class="row1" style="text-align:center;"><a href="'.$admin_file.'.php?op=Donations_View&id='.$d['id'].'">'._DONATIONS_VIEW_LINK.'</a></td>';
		echo '</tr>';
	}
	echo '</table>';
	CloseTable();
}

function donation_details(){
	global $db, $prefix, $admin_file, $module_name;
	$id = (int) $_GET['id'];
	if (strcmp($id, $_GET['id'])!=0){
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_WRONG_ID.'<div>';
		CloseTable();
	}else{
		$res = $db->sql_query('SELECT currency.* FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
		list($currency, $currency_symbol) = $db->sql_fetchrow($res);
		
		$res = $db->sql_query('(SELECT 0 AS `archived`, d.`id`, d.`txn_id`, d.`user_id`, d.`payment_date`, d.`payment_status`, d.`mc_gross`, d.`mc_currency`, d.`settle_amount`, d.`first_name`, d.`last_name`, d.`payer_email`, d.`currency_symbol`, d.`event_id`, u.`username` FROM `'.$prefix.'_donations` AS d LEFT JOIN `'.$prefix.'_users` AS u ON d.`user_id`=u.`user_id` WHERE `id`='.$id.') UNION (SELECT 1 AS `archived`, d.`id`, d.`txn_id`, d.`user_id`, d.`payment_date`, d.`payment_status`, d.`mc_gross`, d.`mc_currency`, d.`settle_amount`, d.`first_name`, d.`last_name`, d.`payer_email`, d.`currency_symbol`, d.`event_id`, u.`username` FROM `'.$prefix.'_donations_archive` AS d LEFT JOIN `'.$prefix.'_users` AS u ON d.`user_id`=u.`user_id` WHERE `id`='.$id.')');
		if ($db->sql_numrows($res)==0){
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_WRONG_ID.'</div>';
			CloseTable();
		}else{
			$row = $db->sql_fetchrow();
			$username = $row['username'];
			if (strcmp($row['txn_id'],0)==0){
				$donation = $row;
			}else{
				
				if ($row['payment_status']=='Refunded'){
					$refund = $row;
					$res = $db->sql_query('SELECT 1 AS `archived`, `id`, `txn_id`, `user_id`, `payment_date`, `payment_status`, `mc_gross`, `mc_currency`, `settle_amount`, `first_name`, `last_name`, `payer_email`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations_archive` WHERE `txn_id`=\''.$refund['txn_id'].'\' AND NOT `id`='.$refund['id'].'');
					$donation = $db->sql_fetchrow($res);
				}else{
					$donation = $row;
					$res = $db->sql_query('SELECT `id`, `txn_id`, `user_id`, `payment_date`, `payment_status`, `mc_gross`, `mc_currency`, `settle_amount`, `first_name`, `last_name`, `payer_email`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations_archive` WHERE `txn_id`=\''.$donation['txn_id'].'\' AND NOT `id`='.$donation['id'].'');
					
					if ($db->sql_numrows($res)!=0){
						$refund = $db->sql_fetchrow($res);
					}
				}
			}
			if ($donation['event_id']==0){
				$event = _DONATIONS_SITE_DONATIONS;
			}else{
				$res = $db->sql_query('SELECT `title` FROM `'.$prefix.'_donations_events` WHERE `id`='.$donation['event_id']);
				list($event) = $db->sql_fetchrow($res);
			}
			
			OpenTable();
			echo '<table style="width:600px;border-spacing:0;margin:0 auto;">';
			echo '<tr><th colspan="4">'._DONATIONS_DETAILS.'<span style="float:right;">';
			if ($donation['archived']!=1){
				echo '<a href="'.$admin_file.'.php?op=Donations_View&action=Archive&id='.$donation['id'].'"><img src="modules/'.$module_name.'/img/archive.png" style="padding-right:10px;border:none;" alt="'._DONATIONS_RECORD_ARCHIVE_ALT.'" title="'._DONATIONS_RECORD_ARCHIVE.'" /></a>';
			}
			echo '<a href="'.$admin_file.'.php?op=Donations_View&action=Delete&id='.$donation['id'].'"><img src="modules/'.$module_name.'/img/delete.png" style="padding-right:10px;border:none;" alt="'._DONATIONS_RECORD_DELETE_ALT.'" title="'._DONATIONS_RECORD_DELETE.'" /></a>';
			echo '</span></th></tr>';
			echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_DONATOR.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:40%;">'.$donation['first_name'].' '.$donation['last_name'].' '.(($donation['user_id']!=0) ? '(<a href="modules.php?name=Profile&mode=viewprofile&u='.$donation['user_id'].'">'.UsernameColor($username).'</a>)' : '').'</td>';
			echo '<td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_AMOUNT.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:20%;">'.$donation['currency_symbol'].currency($donation['mc_gross']).' '.$donation['mc_currency'].'</td></tr>';
			echo '<tr><td class="row2" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_PAYER_EMAIL.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:40%;">'.$donation['payer_email'].'</a></td>';
			echo '<td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_SETTLE_AMOUNT.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:20%;">'.$currency_symbol.currency($donation['settle_amount']).' '.$currency.'</td></tr>';
			echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_DATE.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:40%;">'.date('d/m/Y', $donation['payment_date']).'</a></td>';
			echo '<td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENT.'</td>';
			echo '<td class="row1" style="font-weight:bold;width:20%;">'.$event.'</td></tr>';
			if ($donation['archived']==1){
				echo '<tr><th colspan="4">'._DONATIONS_RECORD_ARCHIVED.'</th></tr>';
			}
			echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_PAYMENT_STATUS.'</td><td class="row2" colspan="3" style="font-weight:bold;">';
			if (isset($refund)){
				echo '<del>';
			}
			echo _DONATIONS_STATUS_COMPLETED;
			if (isset($refund)){
				echo '</del> '._DONATIONS_STATUS_REFUNDED_ON.' '.date('d/m/Y', $refund['payment_date']);
			}
			echo  '</td></tr>';
			echo '</table>';
			CloseTable();
		}
	}
}

function archive_donation(){
	global $db, $prefix, $admin_file;
	$id = (int) $_GET['id'];
	if (strcmp($id, $_GET['id'])!=0){
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_WRONG_ID.'<div>';
		CloseTable();
	}else{
		if (isset($_POST['confirm'])){
			if ($_POST['confirm']==1){
				$db->sql_query('INSERT INTO `'.$prefix.'_donations_archive` (`id`, `user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id`) SELECT `id`,`user_id`, `business`, `txn_id`, `item_name`, `item_number`, `quantity`, `invoice`, `custom`, `tax`, `memo`, `payment_status`, `payment_date`, `txn_type`, `mc_gross`, `mc_fee`, `mc_currency`, `settle_amount`, `exchange_rate`, `first_name`, `last_name`, `payer_email`, `payer_status`, `currency_symbol`, `event_id` FROM `'.$prefix.'_donations` WHERE `id`=\''.$id.'\'');
				$db->sql_query('DELETE FROM `'.$prefix.'_donations` WHERE `id`=\''.$id.'\'');
				OpenTable();
				echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_NOW_ARCHIVED._DONATIONS_CLICK_BACK_1.'<a href="'.$admin_file.'.php?op=Donations_View&id='.$id.'">'._DONATIONS_CLICK_BACK_2.'</a>'._DONATIONS_CLICK_BACK_3.'</div>';
				CloseTable();
			}else{
				OpenTable();
				echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_NOT_ARCHIVED._DONATIONS_CLICK_BACK_1.'<a href="'.$admin_file.'.php?op=Donations_View&id='.$id.'">'._DONATIONS_CLICK_BACK_2.'</a>'._DONATIONS_CLICK_BACK_3.'</div>';
				CloseTable();
			}
			
		}else{
			OpenTable();
			echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_CONFIRM_ARCHIVE.'<br /><br />';
			echo '<form action="'.$admin_file.'.php?op=Donations_View&action=Archive&id='.$id.'" method="post">';
			echo '<select name="confirm">';
			echo '<option value="0">'._DONATIONS_RECORD_DO_NOTHING.'</option>';
			echo '<option value="1">'._DONATIONS_RECORD_ARCHIVE_DONATION.'</option>';
			echo '</select><br /><br />';
			echo '<input type="submit" value="'._DONATIONS_CONTINUE.'" />';
			echo '</form></div>';
			CloseTable();
			
		}
	}
}

function delete_donation(){
	global $db, $prefix, $admin_file;
	$id = (int) $_GET['id'];
	if (strcmp($id, $_GET['id'])!=0){
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_WRONG_ID.'<div>';
		CloseTable();
	}else{
		if (isset($_POST['confirm'])){
			if ($_POST['confirm']==1){
				//Need to delete refunds as well
				$res = $db->sql_query('(SELECT `txn_id`, `event_id`, `settle_amount` FROM `'.$prefix.'_donations` WHERE `id`=\''.$id.'\') UNION (SELECT `txn_id`, `event_id`, 0 FROM `'.$prefix.'_donations_archive` WHERE `id`=\''.$id.'\')');
				list($txn_id, $event_id, $settle) = $db->sql_fetchrow($res);
				if ($txn_id==0){
					$db->sql_query('DELETE FROM `'.$prefix.'_donations` WHERE `id`=\''.$id.'\'');
					$db->sql_query('DELETE FROM `'.$prefix.'_donations_archive` WHERE `id`=\''.$id.'\'');
				}else{
					$db->sql_query('DELETE FROM `'.$prefix.'_donations` WHERE `txn_id`=\''.$txn_id.'\'');
					$db->sql_query('DELETE FROM `'.$prefix.'_donations_archive` WHERE `txn_id`=\''.$txn_id.'\'');
				}
				if ($settle!=0){
					$res = $db->sql_query('SELECT `current` FROM `'.$prefix.'_donations_events` WHERE `id`=\''.$event_id.'\'');
					list($current) = $db->sql_fetchrow($res);
					$new_current = ($current>$settle) ? $current - $settle : 0;
					$db->sql_query('UPDATE `'.$prefix.'_donations_events` SET `current`=\''.$new_current.'\' WHERE `id`=\''.$event_id.'\'');
				}
				
				OpenTable();
				echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_NOW_DELETED._DONATIONS_CLICK_BACK_1.'<a href="'.$admin_file.'.php?op=Donations_View">'._DONATIONS_CLICK_BACK_2.'</a>'._DONATIONS_CLICK_BACK_3.'</div>';
				CloseTable();
			}else{
				OpenTable();
				echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_NOT_DELETED._DONATIONS_CLICK_BACK_1.'<a href="'.$admin_file.'.php?op=Donations_View&id='.$id.'">'._DONATIONS_CLICK_BACK_2.'</a>'._DONATIONS_CLICK_BACK_3.'</div>';
				CloseTable();
			}
			
		}else{
			OpenTable();
			echo '<div style="width:100%;text-align:center;font-weight:bold;">'._DONATIONS_RECORD_CONFIRM_DELETE.'<br /><br />';
			echo '<form action="'.$admin_file.'.php?op=Donations_View&action=Delete&id='.$id.'" method="post">';
			echo '<select name="confirm">';
			echo '<option value="0">'._DONATIONS_RECORD_DO_NOTHING.'</option>';
			echo '<option value="1">'._DONATIONS_RECORD_DELETE_DONATION.'</option>';
			echo '</select><br /><br />';
			echo '<input type="submit" value="'._DONATIONS_CONTINUE.'" />';
			echo '</form></div>';
			CloseTable();
			
		}
	}
}

if (isset($_GET['id'])){
	$action = (isset($_GET['action']) ? $_GET['action'] : '');
	switch ($action){
		case 'Archive': archive_donation(); break;
		case 'Delete': delete_donation(); break;
		default: donation_details();
	}
}else{
	list_donations();
}