<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : events.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

function events_main(){
	global $db, $prefix, $admin_file, $module_name, $donations_xtreme_events_checker_installed;
	
	OpenTable();
	
	$res = $db->sql_query('SELECT currency.`currency`, currency.`currency_symbol`, config.`site_target`, config.`site_active` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
	list($currency, $currency_symbol, $site_target, $site_active) = $db->sql_fetchrow($res);
	
	$res = $db->sql_query('SELECT SUM(`settle_amount`) FROM `'.$prefix.'_donations` WHERE `event_id`=0');
	list($site_donations_amount) = $db->sql_fetchrow($res);
	echo '<table style="width:650px;border-spacing:0;margin: 0 auto;"><tr><th>'._DONATIONS_EVENTS_TITLE.'</th><th>'._DONATIONS_EVENTS_ACTIVE.'</th><th>'._DONATIONS_END_DATE.'</th><th>'._DONATIONS_GOAL.'</th><th>'._DONATIONS_CURRENT_AMOUNT.'</th><th>'._DONATIONS_ACTION.'</th></tr>';
	echo '<tr><td class="row1" style="text-align:center;"><a href="modules.php?name='.$module_name.'&op=Donate&id=0">'._DONATIONS_SITE_DONATIONS.'</a></td>';
	echo '<td class="row1" style="text-align:center;">'.(($site_active) ? _YES : _NO).'</td>';
	echo '<td class="row1" style="text-align:center;">'._DONATIONS_ONGOING.'</td>';
	echo '<td class="row1" style="text-align:center;">'.(($site_target!=0) ? $currency_symbol.currency($site_target).' '.$currency : _DONATIONS_NO_TARGET).'</td>';
	echo '<td class="row1" style="text-align:center;">'.$currency_symbol.((!empty($site_donations_amount)) ? currency($site_donations_amount) : '0.00').' '.$currency.'</td>';
	echo '<td class="row1" style="text-align:center;"><a href="'.$admin_file.'.php?op=Donations_Events&id=0">'._DONATIONS_EDIT_LINK.'</a></td></tr>';
	
	$res = $db->sql_query('SELECT `id`, `active`, `time_based`, `title`, `date_end`, `target`, `current` FROM `'.$prefix.'_donations_events` ORDER BY `active` DESC');
	while($row = $db->sql_fetchrow($res)){
		echo '<tr><td class="row1" style="text-align:center;"><a href="modules.php?name='.$module_name.'&op=Donate&id='.$row['id'].'">'.$row['title'].'</a></td>';
		echo '<td class="row1" style="text-align:center;">'.(($row['active']) ? _YES : _NO).'</td>';
		echo '<td class="row1" style="text-align:center;">';
		echo  (($row['time_based']) ? date('d/m/Y', $row['date_end']) : _DONATIONS_ONGOING);
		echo '</td>';
		echo '<td class="row1" style="text-align:center;">';
		echo  (($row['target']==0) ? _DONATIONS_NO_TARGET : $currency_symbol.currency($row['target']).' '.$currency);
		echo '</td>';
		echo '<td class="row1" style="text-align:center;">'.$currency_symbol.currency($row['current']).' '.$currency.'</td>';
		echo '<td class="row1" style="text-align:center;"><a href="'.$admin_file.'.php?op=Donations_Events&id='.$row['id'].'">'._DONATIONS_EDIT_LINK.'</a> | <a href="'.$admin_file.'.php?op=Donations_Events&action=Delete&id='.$row['id'].'">'._DONATIONS_DELETE_LINK.'</a></td></tr>';
	}
	echo '<tr><td class="row1" colspan="6" style="padding-left:5px;"><a href="'.$admin_file.'.php?op=Donations_Events&action=New">'._DONATIONS_NEW_EVENT.'</a></td></tr>';
	echo '</table>';
	if (!$donations_xtreme_events_checker_installed){
		echo '<br /><div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_CHECKED_NOT_INSTALLED.'</div>';
	}
	CloseTable();
}

function events_form($id, $active, $time_based, $title, $description, $event_length, $date_end, $recurring, $target, $new=false){
	global $db, $prefix, $admin_file;
	$res = $db->sql_query('SELECT currency.`currency`, currency.`currency_symbol` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
	list($currency, $currency_symbol) = $db->sql_fetchrow($res);
	
	if ($new){
		echo '<form action="'.$admin_file.'.php?op=Donations_Events&action=New" method="post">';
	}else{
		echo '<form action="'.$admin_file.'.php?op=Donations_Events&id='.$id.'" method="post">';
	}
	echo '<input type="hidden" name="complete" value="1" />';
	if (!$new){
		echo '<input type="hidden" name="active" value="'.(($active) ? 1 : 0).'" />';
	}
	echo '<table style="width:650px;border-spacing:0;margin: 0 auto;"><tr><th colspan="2">'.(($new) ? _DONATIONS_NEW_EVENT : _DONATIONS_EDIT_EVENT).'</th></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_ACTIVE.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;">';
	if ($new){
		echo '<select name="active"';
		echo '<option value="1">'._YES.'</option>';
		echo '<option value="0">'._NO.'</option>';
		echo '</select>';
	}else{
		echo ($active) ? _DONATIONS_ACTIVE : _DONATIONS_INACTIVE;
		echo '&nbsp;&nbsp;(<a href="'.$admin_file.'.php?op=Donations_Events&id='.$id.'&switch_status='.(($active) ? 0 : 1).'" style="">'._DONATIONS_STATUS_SWITCH.'</a>)</td></tr>';
	}
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_TITLE.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><input type="text" name="title" value="'.$title.'" /></td></tr>';
	
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;vertical-align:top;">'._DONATIONS_EVENTS_DESC.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><textarea name="description" style="width:100%;">'.$description.'</textarea></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_TIME_BASED.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><select name="time_based">';
	echo '<option value="1"'.(($time_based==1) ? ' selected="selected" ' : '').'>'._YES.'</option>';
	echo '<option value="0"'.(($time_based==0) ? ' selected="selected" ' : '').'>'._NO.'</option>';
	echo '</td></tr>';	
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_END_DATE.' *</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><input type="text" name="date_end" value="'.date('d/m/Y',$date_end).'" />&nbsp;&nbsp;(dd/mm/yyyy)</td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_RECURRING.' *</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><select name="recurring">';
	echo '<option value="1"'.(($recurring==1) ? ' selected="selected" ' : '').'>'._YES.'</option>';
	echo '<option value="0"'.(($recurring==0) ? ' selected="selected" ' : '').'>'._NO.'</option>';
	echo '</td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_LENGTH.' *</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;">';
	list($years,$months,$weeks,$days) = explode(',', $event_length);
	echo '<select name="length_years">';
	for ($i=0;$i<=5;$i++){
		echo '<option value="'.$i.'"'.(($i==$years) ? ' selected="selected" ' : '').'>'.$i.' '._DONATIONS_EVENTS_LENGTH_YEARS.'</option>';
	}
	echo '</select>';
	echo '<select name="length_months">';
	for ($i=0;$i<=12;$i++){
		echo '<option value="'.$i.'"'.(($i==$months) ? ' selected="selected" ' : '').'>'.$i.' '._DONATIONS_EVENTS_LENGTH_MONTHS.'</option>';
	}
	echo '</select>';
	echo '<select name="length_weeks">';
	for ($i=0;$i<=52;$i++){
		echo '<option value="'.$i.'"'.(($i==$weeks) ? ' selected="selected" ' : '').'>'.$i.' '._DONATIONS_EVENTS_LENGTH_WEEKS.'</option>';
	}
	echo '</select>';
	echo '<select name="length_days">';
	for ($i=0;$i<=30;$i++){
		echo '<option value="'.$i.'"'.(($i==$days) ? ' selected="selected" ' : '').'>'.$i.' '._DONATIONS_EVENTS_LENGTH_DAYS.'</option>';
	}
	echo '</select>';
	echo '</td></tr>';
	
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_GOAL.' ('.$currency_symbol.')</td>';
	echo '<td class="row1" style="font-weight:bold;width:40%;"><input type="text" name="target" value="'.$target.'" />&nbsp;&nbsp;'._DONATIONS_EVENTS_0_FOR_ONGOING.'</td></tr>';
	echo '<tr><td class="row1" colspan="2" style="text-align:center;">* '._DONATIONS_EVENTS_ONLY_TIME_BASED.'</td></tr>';
	echo '<tr><td class="row1" colspan="2" style="text-align:center;"><input type="submit" value="'._DONATIONS_EVENTS_SAVE_EVENT.'" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

function events_main_donation_form(){
	global $db, $prefix, $admin_file;
	$res = $db->sql_query('SELECT currency.`currency`, currency.`currency_symbol`, config.`site_target`, config.`site_active` FROM `'.$prefix.'_donations_config` AS config LEFT JOIN `'.$prefix.'_donations_currency` AS currency ON config.`currency` = currency.`currency`');
	list($currency, $currency_symbol, $target, $active) = $db->sql_fetchrow($res);
	
	echo '<form action="'.$admin_file.'.php?op=Donations_Events&id=0" method="post">';
	echo '<input type="hidden" name="complete" value="1" />';
	echo '<input type="hidden" name="active" value="'.(($active) ? 1 : 0).'" />';
	echo '<table style="width:650px;border-spacing:0;margin: 0 auto;"><tr><th colspan="2">'._DONATIONS_EDIT_EVENT.'</th></tr>';
	echo '<tr><td class="row2" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_EVENTS_ACTIVE.'</td>';
	echo '<td class="row2" style="font-weight:bold;width:40%;">';
	echo ($active) ? _DONATIONS_ACTIVE : _DONATIONS_INACTIVE;
	echo '&nbsp;&nbsp;(<a href="'.$admin_file.'.php?op=Donations_Events&id=0&switch_status='.(($active) ? 0 : 1).'" style="">'._DONATIONS_STATUS_SWITCH.'</a>)</td></tr>';
	echo '<tr><td class="row2" style="text-align:right;padding-right:5px;width:20%;">'._DONATIONS_GOAL.' ('.$currency_symbol.')</td>';
	echo '<td class="row2" style="font-weight:bold;width:40%;"><input type="text" name="target" value="'.$target.'" />&nbsp;&nbsp;'._DONATIONS_EVENTS_0_FOR_ONGOING.'</td></tr>';
	echo '<tr><td class="row2" colspan="2" style="text-align:center;"><input type="submit" value="'._DONATIONS_EVENTS_SAVE_EVENT.'" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

function events_edit(){
	global $db, $prefix, $admin_file, $module_name, $_GETVAR;
	$id = (int) $_GET['id'];
	if (strcmp($id, $_GET['id'])!=0){
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_WRONG_ID.'</div>';
		CloseTable();
	}else{
		if ($id==0){
			if (isset($_GET['switch_status'])){
				$db->sql_query('UPDATE `'.$prefix.'_donations_config` SET `site_active`='.((int) $_GET['switch_status']));
			}
			if (isset($_POST['complete'])){
				$db->sql_query('UPDATE `'.$prefix.'_donations_config` SET `site_target`='.((int) $_POST['target']));
			}
			OpenTable();
			events_main_donation_form();
			CloseTable();
		}else{
			if (isset($_GET['switch_status'])){
				$db->sql_query('UPDATE `'.$prefix.'_donations_events` SET `active`='.((int) $_GET['switch_status']).' WHERE `id`='.$id);
			}
			
			if (isset($_POST['complete'])){
				$active = (int) $_POST['active'];
				$title = $_GETVAR->get('title', '_POST');
				$description = $_GETVAR->get('description', '_POST');
				$time_based = (int) $_POST['time_based'];
				$length = implode(',', array((int) $_POST['length_years'],(int) $_POST['length_months'],(int) $_POST['length_weeks'],(int) $_POST['length_days']));
				$date_end = $_GETVAR->get('date_end', '_POST');
				$end_date_components = explode('/', $date_end);
				$recurring = (int) $_POST['recurring'];
				$target = (int) $_POST['target'];
				$msg = '';
				if (empty($title)){
					$msg .= _DONATIONS_INVALID_TITLE.'<br />';
				}
				if (empty($description)){
					$msg .= _DONATIONS_INVALID_DESCRIPTION.'<br />';
				}
				if (count($end_date_components)!=3){
					list($day, $month, $year) = explode('/', date('d/m/Y',time()));
					if ($time_based==1){
						$msg .= _DONATIONS_INVALID_DATE.'<br />';
					}
				}else{
					list($day, $month, $year) = explode('/', $date_end);
				}
				$date_end = mktime(0, 0, 0, $month, $day, $year);
				
				if ($recurring&&(((int) $_POST['length_years'] + (int) $_POST['length_months'] + (int) $_POST['length_weeks'] + (int) $_POST['length_days'])==0)){
					$msg .= _DONATIONS_INVALID_LENGTH.'<br />';
				}
				
				
				
				if (empty($msg)){
					if ($db->sql_query('UPDATE `'.$prefix."_donations_events` SET `time_based`='$time_based', `title`='$title', `description`='$description', `event_length`='$length', `date_end`='".$date_end."', `event_recurring`='$recurring', `target`='$target' WHERE `id`=$id")){
						$msg = _DONATIONS_EVENTS_SAVE_SUCCESSFUL;
					}else{
						$msg = _DONATIONS_EVENTS_SAVE_FAILURE;
					}
				}
	
				OpenTable();
	
				echo '<div style="text-align:center;font-weight:bold;">'.$msg.'<br /><br /></div>';
				events_form($id, $active, $time_based, $title, $description, $length, $date_end, $recurring, $target);
				CloseTable();
			}else{
				$res = $db->sql_query('SELECT `active`, `time_based`, `title`, `description`, `event_length`, `date_end`, `event_recurring`, `target` FROM `'.$prefix.'_donations_events` WHERE `id`=\''.$id.'\'');
				
				if ($db->sql_numrows($res)==0){
					OpenTable();
					echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_WRONG_ID.'</div>';
					CloseTable();
				}else{
					list($active, $time_based, $title, $description, $event_length, $date_end, $recurring, $target) = $db->sql_fetchrow($res);
					OpenTable();
					events_form($id, $active, $time_based, $title, $description, $event_length, $date_end, $recurring, $target);
					CloseTable();
				}
			}
		}
	}
}
$action = (isset($_GET['action']) ? $_GET['action'] : '');
switch ($action){
	case 'Delete':
		if (isset($_GET['id'])){
			if (isset($_GET['confirm'])){
				$db->sql_query('DELETE FROM `'.$prefix.'_donations_events` WHERE `id`='.((int) $_GET['id']));
				events_main();
			}else{
				OpenTable();
				echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENT_DELETE_CONFIRM.'<br /><br />';
				echo '<form action="'.$admin_file.'.php?op=Donations_Events&action=Delete&id='.$_GET['id'].'&confirm=1" method="post">';
				echo '<input type="submit" value="'._DONATIONS_EVENT_DELETE_CONFIRM2.'" />';
				echo '</div>';
				CloseTable();
			}
		}else{
			events_main();
		}
	break;
	case 'New':
		if (isset($_POST['complete'])){
			$active = (int) $_POST['active'];
			$title = $_GETVAR->get('title', '_POST');
			$description = $_GETVAR->get('description', '_POST');
			$time_based = (int) $_POST['time_based'];
			$length = implode(',', array((int) $_POST['length_years'],(int) $_POST['length_months'],(int) $_POST['length_weeks'],(int) $_POST['length_days']));
			$date_end = $_GETVAR->get('date_end', '_POST');
			$recurring = (int) $_POST['recurring'];
			$target = (int) $_POST['target'];
			$msg = '';
			
			if (empty($title)){
				$msg .= _DONATIONS_INVALID_TITLE.'<br />';
			}
			if (empty($description)){
				$msg .= _DONATIONS_INVALID_DESCRIPTION.'<br />';
			}
			
			if (empty($msg)){
				list($day, $month, $year) = explode('/', $date_end);
				$date_end = mktime(0, 0, 0, $month, $day, $year);
				
				if ($db->sql_query('INSERT INTO `'.$prefix."_donations_events` (`time_based`, `active`, `title`, `description`, `event_length`, `date_end`, `event_recurring`, `target`) VALUES ($time_based, $active, '$title', '$description', '$length', $date_end, $recurring, $target)")){
					OpenTable();
					echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_SAVE_SUCCESSFUL.'</div>';
					CloseTable();
					events_main();
				}else{
					OpenTable();
					echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_EVENTS_SAVE_FAILURE.'</div>';
					CloseTable();
				}
			}else{
				OpenTable();
				echo '<div style="text-align:center;font-weight:bold;">'.$msg.'</div>';
				events_form(0, $active, $time_based, $title, $description, $event_length, $date_end, $recurring, $target, true);
				CloseTable();
			}
			
			
		}else{
			OpenTable();
			events_form(0,1,1,'','','0,0,0,0',time(),1,0,true);
			CloseTable();
		}
	break;
	default:
		if (isset($_GET['id'])){
			events_edit();
		}else{
			events_main();
		}
}
