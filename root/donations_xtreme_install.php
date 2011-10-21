<?PHP
/*******************************************************************/
/*                  COPYRIGHT NOTICE!                              */
/*This script is designed by DarkForge Graphics and is copyrighted */
/*2010-2011. All rights reserved. Please do not claim this         */
/*      script as yours.DO NOT RE-DISTRIBUTE.                      */
/*          http://www.darkforgegfx.com                            */
/*******************************************************************/
/*               Donations Xtreme Module                           */
/*******************************************************************/

require_once("mainfile.php");
global $admin;

echo "<link rel=\"stylesheet\" href=\"donations_xtreme_install/style/styles.css\" type=\"text/css\"/>\n";

function grm_tableopen(){

echo " <div class=\"grmtop\">";
echo " <div class=\"grmright\">";
echo " <div class=\"grmbot\">";
echo " <div class=\"grmleft\">";
echo " <div class=\"grmlcorner\">";
echo " <div class=\"grmrcorner\">";
echo " <div class=\"grmblcorner\">";
echo " <div class=\"grmbrcorner\">";
echo " <div class=\"innercontent\">";
					
}
function grmtableclose(){

echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";
echo " </div>";

}
echo '<div class="main" align="center">';
if (!is_admin($admin)) { die ("Sorry you are not an administrator, You can not install this module. Log into your site as admin and try again."); }
switch($grm) {
default:
grm_tableopen();
echo '<b> DarkForge Graphics </b><br><br>This module was created by DarkForge Graphics and all copyrights have been withheld. If you did not download this file from <a href="http://www.darkforgegfx.com">http://www.darkforgegfx.com</a> or <a href="http://evolution-xtreme.com">http://evolution-xtreme.com</a> then you are using a copy at your own risk and will not get support for it. We have spent a lot of time making this for you to use. Play fair and support the authors.<br><br>To install the module please click continue below.<br><br><a href="donations_xtreme_install.php?grm=check"><img src="donations_xtreme_install/images/grmcbut.png" border="0"></a>';
grmtableclose();
break;

case "check":
grm_tableopen();
echo '<b>Authenticating Module/Block</b><br><br>';
echo '<div align="left">';

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


$file_found = '<img src="donations_xtreme_install/images/found.png">';
$file_missing = '<img src="donations_xtreme_install/images/missing.png">';

foreach ($filename as $files){

if (file_exists($files)) { 
    echo  'The file: '.$files.' exists '.$file_found.'<br>'; 
} else { 
    echo 'The file: '.$files.' does not exist. '.$file_missing.'<br>'; 
	$stop = 1;
} 
}
echo '</div>';
echo '<br><br><b>Check Complete</b><br><br>';
if($stop){
echo 'You have files that either have not been uploaded or are corrupt. Please make sure the missing files are in the correct location then press refresh to re-do the check again.';
}else{
echo '<a href="donations_xtreme_install.php?grm=install"><img src="donations_xtreme_install/images/grmcbut.png" border="0"></a>';
}
grmtableclose();
break;
 case "install":
grm_tableopen();
echo '<b>MySQL Tables Installation</b><br><br>';
echo '<form action="donations_xtreme_install.php?grm=updatetable" method="post">';
echo '<div class="tabcenter" align="left"><input name="grminstall[]" type="radio" value="1" checked> First Time Install.<br><input name="grminstall[]" type="radio" value="2"> Uninstall</div>';
echo '<input type="hidden" name="grm" value="updatetable">';
echo "<input type=\"image\" src=\"donations_xtreme_install/images/grmcbut.png\" border=\"0\" style=\"border:0px\">";
echo '</form>';		
grmtableclose();
    break;




case "updatetable":
global $db, $user_prefix, $prefix;
while(list($null, $grminst) = each($grminstall)) {
$listinst = $grminst;
}
grm_tableopen();
echo '<div class="tabcenter" align="left">';
if($listinst ==1){//Install tables
$result1 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `business` varchar(255) NOT NULL DEFAULT '0',
  `txn_id` varchar(20) NOT NULL DEFAULT '0',
  `item_name` varchar(60) NOT NULL DEFAULT '0',
  `item_number` varchar(40) NOT NULL DEFAULT '0',
  `quantity` varchar(6) NOT NULL DEFAULT '0',
  `invoice` varchar(40) NOT NULL DEFAULT '0',
  `custom` varchar(127) NOT NULL DEFAULT '0',
  `tax` varchar(10) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `payment_status` varchar(15) NOT NULL DEFAULT '0',
  `payment_date` int(10) NOT NULL DEFAULT '0',
  `txn_type` varchar(15) NOT NULL DEFAULT '0',
  `mc_gross` varchar(10) NOT NULL DEFAULT '0',
  `mc_fee` varchar(10) NOT NULL DEFAULT '0',
  `mc_currency` varchar(5) NOT NULL DEFAULT '0',
  `settle_amount` varchar(12) NOT NULL DEFAULT '0',
  `exchange_rate` varchar(10) NOT NULL DEFAULT '0',
  `first_name` varchar(127) NOT NULL DEFAULT '0',
  `last_name` varchar(127) NOT NULL DEFAULT '0',
  `payer_email` varchar(127) NOT NULL DEFAULT '0',
  `payer_status` varchar(15) NOT NULL DEFAULT '0',
  `currency_symbol` varchar(7) NOT NULL DEFAULT '$',
  `event_id` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) TYPE=MyISAM");

$result2 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations_archive` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `business` varchar(255) NOT NULL DEFAULT '0',
  `txn_id` varchar(20) NOT NULL DEFAULT '0',
  `item_name` varchar(60) NOT NULL DEFAULT '0',
  `item_number` varchar(40) NOT NULL DEFAULT '0',
  `quantity` varchar(6) NOT NULL DEFAULT '0',
  `invoice` varchar(40) NOT NULL DEFAULT '0',
  `custom` varchar(127) NOT NULL DEFAULT '0',
  `tax` varchar(10) NOT NULL DEFAULT '0',
  `memo` text NOT NULL,
  `payment_status` varchar(15) NOT NULL DEFAULT '0',
  `payment_date` int(10) NOT NULL DEFAULT '0',
  `txn_type` varchar(15) NOT NULL DEFAULT '0',
  `mc_gross` varchar(10) NOT NULL DEFAULT '0',
  `mc_fee` varchar(10) NOT NULL DEFAULT '0',
  `mc_currency` varchar(5) NOT NULL DEFAULT '0',
  `settle_amount` varchar(12) NOT NULL DEFAULT '0',
  `exchange_rate` varchar(10) NOT NULL DEFAULT '0',
  `first_name` varchar(127) NOT NULL DEFAULT '0',
  `last_name` varchar(127) NOT NULL DEFAULT '0',
  `payer_email` varchar(127) NOT NULL DEFAULT '0',
  `payer_status` varchar(15) NOT NULL DEFAULT '0',
  `currency_symbol` varchar(7) NOT NULL DEFAULT '$',
  `event_id` smallint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) TYPE=MyISAM");

$result3 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations_config` (
  `receiver_email` varchar(50) NOT NULL,
  `use_curl` tinyint(1) NOT NULL,
  `sandbox` tinyint(1) NOT NULL,
  `currency` varchar(5) NOT NULL,
  `site_active` tinyint(1) NOT NULL,
  `site_target` int(11) NOT NULL,
  `meter_border` varchar(8) NOT NULL,
  `meter_background` varchar(8) NOT NULL,
  `meter_text` varchar(8) NOT NULL,
  `donations_per_page` int(11) NOT NULL,
  `preset_amounts` text NOT NULL,
  `paypal_languages` text NOT NULL,
  `ipn_url` varchar(127) NOT NULL,
  `return_url` varchar(127) NOT NULL,
  `cancel_url` varchar(127) NOT NULL
) TYPE=MyISAM");

$result4 = $db->sql_query("INSERT INTO `".$prefix."_donations_config` (`receiver_email`, `use_curl`, `sandbox`, `currency`, `site_active`, `site_target`, `meter_border`, `meter_background`, `meter_text`, `donations_per_page`, `preset_amounts`, `paypal_languages`, `ipn_url`, `return_url`, `cancel_url`) VALUES ('', 1, 0, 'USD', 1, 0, '#000000', '#ffffff', '#000000', 10, '5,10,25,50,75,100,200', 'EN|English,DE|Deutsch,ES|Español,FR|Français,IT|Italiano', '".$nukeurl."/donations_ipn.php', '".$nukeurl."/modules.php?name=Donations&op=thankyou', '".$nukeurl."/')");

$result5 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations_currency` (
  `currency` varchar(5) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  PRIMARY KEY (`currency`)
) TYPE=MyISAM");

$result6 = $db->sql_query("INSERT INTO `".$prefix."_donations_currency` (`currency`, `currency_symbol`) VALUES ('USD', '$'), ('AUD', '$'), ('EUR', '&euro;'), ('GBP', '&pound;'), ('CAD', '$'), ('JPY', '&yen;'), ('MXN', '$')");

$result7 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `time_based` smallint(1) NOT NULL DEFAULT '1',
  `active` smallint(1) NOT NULL DEFAULT '1',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `event_length` varchar(32) NOT NULL DEFAULT '0',
  `date_end` int(11) NOT NULL DEFAULT '0',
  `event_recurring` tinyint(1) NOT NULL DEFAULT '1',
  `target` varchar(11) NOT NULL DEFAULT '0',
  `current` varchar(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) TYPE=MyISAM");

if (!$result1) { echo "- Create ".$prefix."_donations, Failed! Table already exists<br>\n"; $grmtop =1;} else { echo "- Create ".$prefix."_donations Table created Successfully<br>\n";
}

if (!$result2) { echo "- Create ".$prefix."_donations_archive, Failed! Table already exists<br>\n"; $grmtop =1;} else { echo "- Create ".$prefix."_donations_archive Table created Successfully<br>\n";
}

if (!$result3) { echo "- Create ".$prefix."_donations_config, Failed! Table already exists<br>\n"; $grmtop =1;} else { echo "- Create ".$prefix."_donations_config Table created Successfully<br>\n";
}

if (!$result4) { echo "- Inserting default configuration into ".$prefix."_donations_config, Failed!<br>\n"; $grmtop =1;} else { echo "- Default configuration inserted into ".$prefix."_donations_config Successfully<br>\n";
}

if (!$result5) { echo "- Create ".$prefix."_donations_currency, Failed! Table already exists<br>\n"; $grmtop =1;} else { echo "- Create ".$prefix."_donations_currency Table created Successfully<br>\n";
}

if (!$result6) { echo "- Inserting default currencies into ".$prefix."_donations_currency, Failed!<br>\n"; $grmtop =1;} else { echo "- Default currencies inserted into ".$prefix."_donations_currency Successfully<br>\n";
}

if (!$result7) { echo "- Create ".$prefix."_donations_events, Failed! Table already exists<br>\n"; $grmtop =1;} else { echo "- Create ".$prefix."_donations_events Table created Successfully<br>\n";
}

}else if($listinst ==2){//uninstall tables
$result1 = $db->sql_query("DROP TABLE `".$prefix."_donations`");
$result1 = $db->sql_query("DROP TABLE `".$prefix."_donations_archive`");
$result1 = $db->sql_query("DROP TABLE `".$prefix."_donations_config`");
$result1 = $db->sql_query("DROP TABLE `".$prefix."_donations_currency`");
$result1 = $db->sql_query("DROP TABLE `".$prefix."_donations_events`");
if (!$result) { echo "Donations Xtreme Uninstall Failed<br>\n"; } else { echo "Donations Xtreme Uninstall successful<br>\n"; }

echo '<br> Please delete the donations_xtreme_install folder and all the files inside and also donations_xtreme_install.php';
}
echo '</div>';
if($grmtop){
echo 'There seems to be tables with the same name already installed. You can remedy this issue by using the un-install option.';
}else{
if($listinst ==1){
echo '<a href="donations_xtreme_install.php?grm=languages"><img src="donations_xtreme_install/images/grmcbut.png" border="0"></a>';
}
}
grmtableclose();
break;

case "languages":
grm_tableopen();
echo '<b>Important Notice!</b><br><br>';
echo '<form action="" method="post">';
echo '<div class="tabcenter" align="left">';

echo '/*******************************************************************/<br />
/*                  COPYRIGHT NOTICE!                              */<br />
/*This script is designed by DarkForge Graphics and is copyrighted */<br />
/*2010-2011. All rights reserved. Please do not claim this         */<br />
/*      script as yours.DO NOT RE-DISTRIBUTE.                      */<br />
/*          http://www.darkforgegfx.com                            */<br />
/*******************************************************************/<br />
/*               Donations Xtreme Module                           */<br />
/*******************************************************************/';
echo '<br />';

echo '</div>';
echo '</form>';
echo '<br><br>';
echo '<a href="donations_xtreme_install.php?grm=finish"><img src="donations_xtreme_install/images/grmcbut.png" border="0"></a>';
grmtableclose();
break;

case "finish":
grm_tableopen();
echo '<b>Congratulations! Your new module has successfully been installed.</b><br><br>Please delete the donations_xtreme_install folder and all it\'s content. Delete also the donations_xtreme_install.php file for security reasons.<br><br>To use this module, the jQuery framework mod must be installed on your site.<br>To get the most out of this module, the Event Checker and Profile Addon mods must also be installed on your site.<br>These are all included in the Donations Xtreme Download.<br><br>All of the above mods &copy; <a href="http://www.darkforgegfx.com">DarkForge Graphics</a> and <a href="http://www.kombatkoding.com">Kombat Koding</a>.<br><br>Please visit the following link for instructions on how to setup the block at <a href="http://www.darkforgegfx.com">DarkForge Graphics.</a><br><br>Enjoy your module and please make a donation to <a href="http://darkforgegfx.com">DarkForge Graphics</a> or <a href="http://evolution-xtreme.com">Evolution-Xtreme</a> if you like this module<br /><br />Thank you,<br />DFG Developemnt Team.';

grmtableclose();
break;

}
echo '</div>';

?>