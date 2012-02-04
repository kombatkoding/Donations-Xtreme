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

function recurse_copy($src,$dst) {
	$dir = opendir($src);
	@mkdir($dst);
	while(false !== ( $file = readdir($dir)) ) {
		if (( $file != '.' ) && ( $file != '..' )) {
			if ( is_dir($src . '/' . $file) ) {
				recurse_copy($src . '/' . $file,$dst . '/' . $file);
			} else {
				copy($src . '/' . $file,$dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

$dirs = array();
$dirs[] = 'modules/Donations/';
$dirs[] = 'modules/Donations/admin/';
$dirs[] = 'modules/Donations/img/';
$dirs[] = 'modules/Donations/language/';

$filename = array();
$filename[] = 'donations_ipn.php';
$filename[] = 'modules/Donations/admin/index.php';
$filename[] = 'modules/Donations/admin/case.php';
$filename[] = 'modules/Donations/admin/links.php';
$filename[] = 'modules/Donations/admin/blocks.php';
$filename[] = 'modules/Donations/admin/config.php';
$filename[] = 'modules/Donations/admin/events.php';
$filename[] = 'modules/Donations/admin/view.php';
$filename[] = 'modules/Donations/index.php';
$filename[] = 'modules/Donations/block.php';
$filename[] = 'modules/Donations/copyright.php';
$filename[] = 'modules/Donations/donate.php';
$filename[] = 'modules/Donations/events_last_checked.php';
$filename[] = 'modules/Donations/functions.php';
$filename[] = 'modules/Donations/ipn.php';
$filename[] = 'modules/Donations/main.php';
$filename[] = 'modules/Donations/thankyou.php';
$filename[] = 'modules/Donations/img/archive.png';
$filename[] = 'modules/Donations/img/btn_donate_large.gif';
$filename[] = 'modules/Donations/img/btn_donate_small.gif';
$filename[] = 'modules/Donations/img/delete.png';
$filename[] = 'modules/Donations/img/donations_logo.png';
$filename[] = 'modules/Donations/language/lang-english.php';
$filename[] = 'images/admin/donations.png';

foreach ($dirs as $d){
	@mkdir(NUKE_BASE_DIR.$d);
}

foreach ($filename as $f){
	@copy(NUKE_BASE_DIR.'donations_xtreme_install/files/'.$f,NUKE_BASE_DIR.$f);
}

OpenTable();
echo '<p><b>Copying Donations Xtreme files</b></p>';
echo '<p>Done</p>';
echo '<p><b>Verifying files</b></p>';
echo '<p>';
foreach ($filename as $file){
	if (file_exists($file)) { 
		echo '<img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">File '.$file.' exists.</span><br />';
	} else { 
		echo '<img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">File '.$file.' does not exist. Please upload manually.</span><br />';
	}
}
echo '</p>';
CloseTable();

global $admin_file;

OpenTable();
echo '<p><b>Installation Complete!</b></p>';
echo '<p>You may now delete the donations_xtreme_install.php file and donations_xtreme_install/ folder.</p>';
echo '<p>Click <a href="'.$admin_file.'.php?op=Donations">here</a> to continue to the admin panel.</p>';
CloseTable();
?>