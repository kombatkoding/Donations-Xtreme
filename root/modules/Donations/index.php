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

if (!defined('MODULE_FILE')){
	die ("You can't access this file directly...");
}

$module_name = basename(dirname(__FILE__));
include_once NUKE_MODULES_DIR.$module_name.'/functions.php';
include_once NUKE_MODULES_DIR.$module_name.'/language/lang-'.$currentlang.'.php';
include NUKE_BASE_DIR.'header.php';

OpenTable();
echo '<div style="text-align:center;"><a href="modules.php?name='.$module_name.'"><img style="width:300px;height:80px;border:0;" src="modules/'.$module_name.'/img/donations_logo.png" title="'._DONATIONS.'" alt="'._DONATIONS.'" /></a></div>';
CloseTable();
echo '<br />';

switch (strtolower($_GET['op'])){
	case 'donate': include NUKE_MODULES_DIR.$module_name.'/donate.php'; break;
	case 'thankyou': include NUKE_MODULES_DIR.$module_name.'/thankyou.php'; break;
	default: include NUKE_MODULES_DIR.$module_name.'/main.php';
}
include NUKE_BASE_DIR.'footer.php';