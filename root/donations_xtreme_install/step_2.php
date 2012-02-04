<?php
/*******************************************************************/
/*                  COPYRIGHT NOTICE!                              */
/*This script is designed by DarkForge Graphics and is copyrighted */
/*2010-2011. All rights reserved. Please do not claim this         */
/*      script as yours.DO NOT RE-DISTRIBUTE.                      */
/*          http://www.darkforgegfx.com                            */
/*******************************************************************/
/*               Donations Xtreme Module                           */
/*******************************************************************/

function recursiveDelete($str){
	return true;
	if(is_file($str)){
		return @unlink($str);
	}
	elseif(is_dir($str)){
		$scan = glob(rtrim($str,'/').'/*');
		foreach($scan as $index=>$path){
			recursiveDelete($path);
		}
		return @rmdir($str);
	}
}

OpenTable();
echo '<p><b>Removing previous Donations module</b></p>';

global $db, $prefix, $cache;

@unlink(NUKE_BLOCKS_DIR.'block-Donations.php');
$db->sql_query('DELETE FROM `'.$prefix.'_blocks` WHERE `blockfile`=\'block-Donations.php\'');
$cache->clear();

$deleted_files = recursiveDelete(NUKE_BASE_DIR.'modules/Donations');
$deleted_sql = ($db->sql_query('DROP TABLE `'.$prefix.'_donators`'))&&($db->sql_query('DROP TABLE `'.$prefix.'_donators_config`'));

echo '<p>';
if ($deleted_files){
	echo '<img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Deleted files from modules/Donations/</span>';
}else{
	echo '<img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Could not delete files from modules/Donations/. Please delete these files manually before you continue.</span>';
}
if ($deleted_sql){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Deleted MySQL tables.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">An error occurred whilst deleting the MySQL tables. Please delete these before you continue.</span>';
}
echo '</p>';

if (!$deleted_files||!$deleted_sql){
echo '<p>Please fix the above errors before continuing to the next page.</p>';
}
echo '<p>Click <a href="donations_xtreme_install.php?step=3">here</a> to continue.</p>';
CloseTable();
?>