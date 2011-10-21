<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : case.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('ADMIN_FILE')) {
    die('Access Denied');
}

$module_name = basename(dirname(dirname(__FILE__)));

switch($op){
    case 'Donations':
    case 'Donations_Blocks':
    case 'Donations_Config':
    case 'Donations_Events':
    case 'Donations_View':
        include(NUKE_MODULES_DIR.$module_name.'/admin/index.php');
    break;

}

?>