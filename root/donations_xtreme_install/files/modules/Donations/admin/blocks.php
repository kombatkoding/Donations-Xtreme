<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : blocks.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

function blocks_main(){
	global $db, $prefix, $admin_file, $module_name, $donations_xtreme_events_checker_installed;
	
	OpenTable();
	$blocks = array();
	if ($handle = opendir(NUKE_BLOCKS_DIR)) {
		while (false !== ($file = readdir($handle))) {
			if (substr($file,0,16)=='block-Donations_'&&$file!='block-Donations_0.php') {
				include (NUKE_BLOCKS_DIR.'/'.$file);
				$blocks[] = array_merge(array('name'=>substr($file,16,-4)),$data);
			}
		}
		closedir($handle);
	}
	echo '<table style="width:650px;border-spacing:0;margin: 0 auto;"><tr><th>'._DONATIONS_BLOCKS_NAME.'</th><th>'._DONATIONS_BLOCKS_EVENT.'</th><th>'._DONATIONS_ACTION.'</th></tr>';
	if (empty($blocks)){
		echo '<tr><td class="row1" colspan="3" style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_DONT_EXIST.'</a></td></tr>';
	}else{
		$events = array(0=>_DONATIONS_SITE_DONATIONS);
		$res = $db->sql_query('SELECT `id`, `title` FROM `'.$prefix.'_donations_events`');
		while($row = $db->sql_fetchrow($res)){
			$events[$row['id']] = $row['title'];
		}
		foreach($blocks as $b){
			echo '<tr><td class="row1" style="text-align:center;">'.$b['name'].'</td>';
			echo '<td class="row1" style="text-align:center;">'.(($b['id']==-1) ? _DONATIONS_BLOCKS_RANDOM : $events[$b['id']]).'</td>';
			echo '<td class="row1" style="text-align:center;"><a href="'.$admin_file.'.php?op=Donations_Blocks&id='.$b['name'].'">'._DONATIONS_EDIT_LINK.'</a> | <a href="'.$admin_file.'.php?op=Donations_Blocks&action=Delete&id='.$b['name'].'">'._DONATIONS_DELETE_LINK.'</a></td></tr>';
		}
	}
	echo '<tr><td class="row1" colspan="6" style="padding-left:5px;"><a href="'.$admin_file.'.php?op=Donations_Blocks&action=New">'._DONATIONS_BLOCKS_NEW.'</a> | <a href="'.$admin_file.'.php?op=blocks">'._DONATIONS_BLOCKS_ADMIN.'</a></td></tr>';
	echo '</table>';
	
	
	CloseTable();
}

function blocks_form($name='', $event=0, $donate_page=false, $show_desc=true, $show_end_date=true, $show_total_donated=true, $show_total_fees=true, $show_net_donations=true, $show_target=true, $show_below_goal=true, $show_currency=true, $bar_position='middle', $donators_to_show=5, $show_date=false, $new=true){
	global $db, $prefix, $admin_file;
	
	if ($new){
		echo '<form action="'.$admin_file.'.php?op=Donations_Blocks&action=New" method="post">';
	}else{
		echo '<form action="'.$admin_file.'.php?op=Donations_Blocks&id='.$name.'" method="post">';
	}
	echo '<input type="hidden" name="complete" value="1" />';
	echo '<table style="width:550px;border-spacing:0;margin: 0 auto;"><tr><th colspan="2">'.(($data==null) ? _DONATIONS_BLOCKS_NEW : _DONATIONS_BLOCKS_EDIT).'</th></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_NAME.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><input type="text" name="name" value="'.$name.'" /></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_EVENT.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="event">';
	echo '<option value="-1"'.(($event==-1) ? ' selected="selected"' : '').'>'._DONATIONS_BLOCKS_RANDOM.'</option><option value="0"'.(($event==0) ? ' selected="selected"' : '').'>'._DONATIONS_SITE_DONATIONS.'</options>';
	$events = array(0=>_DONATIONS_SITE_DONATIONS);
	$res = $db->sql_query('SELECT `id`, `title` FROM `'.$prefix.'_donations_events`');
	while($row = $db->sql_fetchrow($res)){
		echo '<option value="'.$row['id'].'"'.(($event==$row['id']) ? ' selected="selected"' : '').'>'.$row['title'].'</options>';
	}
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_DONATE_PAGE.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="donate_page">';
	echo '<option value="1"'.(($donate_page==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($donate_page==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_DESC.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_desc">';
	echo '<option value="1"'.(($show_desc==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_desc==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_END_DATE.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_end_date">';
	echo '<option value="1"'.(($show_end_date==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_end_date==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_TOTAL_DONATED.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_total_donated">';
	echo '<option value="1"'.(($show_total_donated==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_total_donated==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_TOTAL_FEES.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_total_fees">';
	echo '<option value="1"'.(($show_total_fees==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_total_fees==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_NET_DONATIONS.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_net_donations">';
	echo '<option value="1"'.(($show_net_donations==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_net_donations==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_TARGET.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_target">';
	echo '<option value="1"'.(($show_target==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_target==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_BELOW_GOAL.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_below_goal">';
	echo '<option value="1"'.(($show_below_goal==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_below_goal==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_CURRENCY.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_currency">';
	echo '<option value="1"'.(($show_currency==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_currency==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_BAR_POSITION.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="bar_position">';
	echo '<option value="0"'.(($bar_position==0) ? ' selected="selected"' : '').'>'._DONATIONS_BLOCKS_BAR_DONT_SHOW.'</option>';
	echo '<option value="top"'.(($bar_position=='top') ? ' selected="selected"' : '').'>'._DONATIONS_BLOCKS_BAR_TOP.'</option>';
	echo '<option value="middle"'.(($bar_position=='middle') ? ' selected="selected"' : '').'>'._DONATIONS_BLOCKS_BAR_MIDDLE.'</option>';
	echo '<option value="bottom"'.(($bar_position=='bottom') ? ' selected="selected"' : '').'>'._DONATIONS_BLOCKS_BAR_BOTTOM.'</option>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_DONATORS_TO_SHOW.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><input type="text" name="donators_to_show" value="'.$donators_to_show.'" /></td></tr>';
	echo '<tr><td class="row1" style="text-align:right;padding-right:5px;width:50%;">'._DONATIONS_BLOCKS_SHOW_DATE.'</td>';
	echo '<td class="row1" style="font-weight:bold;width:50%;"><select name="show_date">';
	echo '<option value="1"'.(($show_date==1) ? ' selected="selected"' : '').'>'._YES.'</option><option value="0"'.(($show_date==0) ? ' selected="selected"' : '').'>'._NO.'</options>';
	echo '</select></td></tr>';
	echo '<tr><td class="row1" colspan="2" style="text-align:center;"><input type="submit" value="'._DONATIONS_BLOCKS_SAVE.'" /></td></tr>';
	echo '</table>';
	echo '</form>';
}

function blocks_edit(){
	global $db, $prefix, $admin_file, $module_name, $_GETVAR;
	$id = end(explode('/',$_GET['id']));
	if (isset($id)&&file_exists(NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php')){
		if (isset($_POST['complete'])){
			blocks_save();
		}else{
			OpenTable();
			include (NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php');
			blocks_form($id,$data['id'],$data['donate_page'],$data['show_desc'],$data['show_end_date'],$data['show_total_donated'],$data['show_total_fees'],$data['show_net_donations'],$data['show_target'],$data['show_below_goal'],$data['show_currency'],$data['bar_position'],$data['donators_to_show'],$data['show_date'],false);
			CloseTable();
		}
	}else{
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_WRONG_ID.'</div>';
		CloseTable();
	}
}

function blocks_save($new=false){
	global $_GETVAR, $module_name;
	$id = end(explode('/',$_GETVAR->get('name', '_POST')));
	$id = preg_replace('/[^\w-]/', '', $id);
	$event=(int) $_POST['event'];
	$donate_page=((int) $_POST['donate_page']) ? 'true' : 'false';
	$show_desc=((int) $_POST['show_desc']) ? 'true' : 'false';
	$show_end_date=((int) $_POST['show_end_date']) ? 'true' : 'false';
	$show_total_donated=((int) $_POST['show_total_donated']) ? 'true' : 'false';
	$show_total_fees=((int) $_POST['show_total_fees']) ? 'true' : 'false';
	$show_net_donations=((int) $_POST['show_net_donations']) ? 'true' : 'false';
	$show_target=((int) $_POST['show_target']) ? 'true' : 'false';
	$show_below_goal=((int) $_POST['show_below_goal']) ? 'true' : 'false';
	$show_currency=((int) $_POST['show_currency']) ? 'true' : 'false';
	$bar_position=$_GETVAR->get('bar_position', '_POST');
	$donators_to_show=(int) $_POST['donators_to_show'];
	$show_date=((int) $_POST['show_date']) ? 'true' : 'false';
	
	$msg = '';
	if (empty($id)){
		$msg .= _DONATIONS_INVALID_TITLE.'<br />';
	}
	if ($new&&file_exists(NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php')){
		$msg .= _DONATIONS_INVALID_TITLE_EXISTS.'<br />';
	}
	
	
	if (empty($msg)){
		if ($new==false){
			$old_id = end(explode('/',$_GET['id']));
			if ($id!=$old_id&&file_exists(NUKE_BLOCKS_DIR.'/block-Donations_'.$old_id.'.php')){
				unlink(NUKE_BLOCKS_DIR.'/block-Donations_'.$old_id.'.php');
			}
		}
		if ($f=fopen(NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php','w')){
			fwrite($f, '<?php'."\r\n");
			fwrite($f, '//automatically generated file, please use block manager to edit'."\r\n");
			fwrite($f, '$data[\'id\']='.$event.';');
			fwrite($f, '$data[\'donate_page\']='.$donate_page.';');
			fwrite($f, '$data[\'show_desc\']='.$show_desc.';');
			fwrite($f, '$data[\'show_end_date\']='.$show_end_date.';');
			fwrite($f, '$data[\'show_total_donated\']='.$show_total_donated.';');
			fwrite($f, '$data[\'show_total_fees\']='.$show_total_fees.';');
			fwrite($f, '$data[\'show_net_donations\']='.$show_net_donations.';');
			fwrite($f, '$data[\'show_target\']='.$show_target.';');
			fwrite($f, '$data[\'show_below_goal\']='.$show_below_goal.';');
			fwrite($f, '$data[\'show_currency\']='.$show_currency.';');
			fwrite($f, '$data[\'bar_position\']=\''.$bar_position.'\';');
			fwrite($f, '$data[\'donators_to_show\']='.$donators_to_show.';');
			fwrite($f, '$data[\'show_date\']='.$show_date.';');
			fwrite($f, '$data[\'module\']=\''.$module_name.'\';');
			fwrite($f, 'if (!defined(\'DONATIONS_BLOCK_MANAGER\')){');
			fwrite($f, '	include NUKE_MODULES_DIR.\'/\'.$data[\'module\'].\'/block.php\';');
			fwrite($f, '}');
			
			fclose($f);
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_SAVE_SUCCESSFUL.'</div>';
			CloseTable();
			blocks_main();
		}else{
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_SAVE_FAILURE.'</div>';
			CloseTable();
		}
	}else{
		OpenTable();
		echo '<div style="text-align:center;font-weight:bold;">'.$msg.'</div>';
		blocks_form(0, $active, $time_based, $title, $description, $event_length, $date_end, $recurring, $target, true);
		CloseTable();
	}	
}

define('DONATIONS_BLOCK_MANAGER',true);
$action = (isset($_GET['action']) ? $_GET['action'] : '');
switch ($action){
	case 'Delete':
		$id = end(explode('/',$_GET['id']));
		if (isset($id)&&file_exists(NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php')){
			if (isset($_GET['confirm'])){
				unlink(NUKE_BLOCKS_DIR.'/block-Donations_'.$id.'.php');
				blocks_main();
			}else{
				OpenTable();
				echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_DELETE_CONFIRM.'<br /><br />';
				echo '<form action="'.$admin_file.'.php?op=Donations_Blocks&action=Delete&id='.$id.'&confirm=1" method="post">';
				echo '<input type="submit" value="'._DONATIONS_BLOCKS_DELETE_CONFIRM2.'" />';
				echo '</div>';
				CloseTable();
			}
		}else{
			OpenTable();
			echo '<div style="text-align:center;font-weight:bold;">'._DONATIONS_BLOCKS_WRONG_ID.'</div>';
			CloseTable();
		}
	break;
	case 'New':
		if (isset($_POST['complete'])){
			blocks_save(true);
		}else{
			OpenTable();
			blocks_form();
			CloseTable();
		}
	break;
	default:
		if (isset($_GET['id'])){
			blocks_edit();
		}else{
			blocks_main();
		}
}
