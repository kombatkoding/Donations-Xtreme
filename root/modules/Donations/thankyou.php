<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : thankyou.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

if (!defined('MODULE_FILE')){
	die ("You can't access this file directly...");
}

OpenTable();
echo '<div style="text-align:center;font-weight:bold;">';
echo '<div style="font-size:16px;">';
echo _DONATIONS_THANKYOU_1;
echo '</div>';
echo '<br />';
echo _DONATIONS_THANKYOU_2;
echo '<br /><br />';
echo _DONATIONS_THANKYOU_3_1.'<a href="modules.php?name='.$module_name.'">'._DONATIONS_THANKYOU_3_2.'</a>'._DONATIONS_THANKYOU_3_3;
echo '</div>';
CloseTable();