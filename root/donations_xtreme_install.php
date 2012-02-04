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

require_once "mainfile.php";
global $admin;

if (!is_admin($admin)){
	exit('Error: You do not have permission to install this module');
}

$total_steps = 4;
$step = (isset($_GET['step']) ? (int) $_GET['step'] : 1);
if (($step<1)||($step>$total_steps)){
	$step = 1;
}

include NUKE_BASE_DIR.'header.php';
OpenTable();
echo '<div style="width:100%;text-align:center;font-size:16px;font-weight:bold;">Donations Xtreme v1.3 Installer</div>';
echo '<div style="float:left;">Step:&nbsp;';
if ($step>1){
	for ($i=1;$i<=$total_steps;$i++){
		echo (($i==$step) ? '<b>' : '').$i.(($i==$step) ? '</b>' : '');
		if ($i<$total_steps){
			echo '&nbsp;|&nbsp;';
		}
	}
}else{
	echo 'Introduction';
}
echo '</div><div style="float:right;"><a href="http://evolution-xtreme.com/modules.php?name=Forums&file=viewtopic&t=4469">Help</a></div>';
CloseTable();

include 'donations_xtreme_install/step_'.$step.'.php';
include NUKE_BASE_DIR.'footer.php';

?>