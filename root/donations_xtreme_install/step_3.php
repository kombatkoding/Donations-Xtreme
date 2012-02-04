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


OpenTable();
echo '<p><b>Installing Donations Xtreme MySQL tables.</b></p>';

global $db, $prefix;

$result1 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations` (`id` int(11) NOT NULL AUTO_INCREMENT,`user_id` int(11) NOT NULL DEFAULT '0',`business` varchar(255) NOT NULL DEFAULT '0',
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
)");

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
)");

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
)");

$result4 = $db->sql_query("INSERT INTO `".$prefix."_donations_config` (`receiver_email`, `use_curl`, `sandbox`, `currency`, `site_active`, `site_target`, `meter_border`, `meter_background`, `meter_text`, `donations_per_page`, `preset_amounts`, `paypal_languages`, `ipn_url`, `return_url`, `cancel_url`) VALUES ('', 1, 0, 'USD', 1, 0, '#000000', '#ffffff', '#000000', 10, '5,10,25,50,75,100, 200', 'EN|English,DE|Deutsch,ES|Español,FR|Français,IT|Italiano', '".$nukeurl."/donations_ipn.php', '".$nukeurl."/modules.php?name=Donations&op=thankyou', '".$nukeurl."/')");

$result5 = $db->sql_query("CREATE TABLE IF NOT EXISTS `".$prefix."_donations_currency` (
  `currency` varchar(5) NOT NULL,
  `currency_symbol` varchar(10) NOT NULL,
  PRIMARY KEY (`currency`)
)");

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
)");

echo '<p>';

if ($result1!==false){
	echo '<img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Created table `'.$prefix.'_donations`.</span>';
}else{
	echo '<img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to create table `'.$prefix.'_donations`.</span>';
}

if ($result2!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Created table `'.$prefix.'_donations_archive`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to create table `'.$prefix.'_donations_archive`.</span>';
}

if ($result3!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Created table `'.$prefix.'_donations_config`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to create table `'.$prefix.'_donations_config`.</span>';
}

if ($result4!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Inserted data into `'.$prefix.'_donations_config`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to insert data into `'.$prefix.'_donations_config`.</span>';
}

if ($result5!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Created table `'.$prefix.'_donations_currency`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to create table `'.$prefix.'_donations_currency`.</span>';
}

if ($result6!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Inserted data into `'.$prefix.'_donations_config`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to insert data into `'.$prefix.'_donations_config`.</span>';
}

if ($result7!==false){
	echo '<br /><img src="donations_xtreme_install/images/success.png">&nbsp;<span style="position:relative;bottom:4px;">Created table `'.$prefix.'_donations_events`.</span>';
}else{
	echo '<br /><img src="donations_xtreme_install/images/error.png">&nbsp;<span style="position:relative;bottom:4px;">Failed to create table `'.$prefix.'_donations_events`.</span>';
}

if (!$result1||!$result2||!$result3||!$result4||!$result5||!$result6||!$result7){
echo '<p>Please manually create the MySQL tables before continuing.</p>';
}
echo '<p>Click <a href="donations_xtreme_install.php?step=4">here</a> to continue.</p>';
CloseTable();
?>