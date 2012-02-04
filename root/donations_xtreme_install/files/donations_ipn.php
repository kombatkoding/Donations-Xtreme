<?php
/*=======================================================================
 Nuke-Evolution Basic: Enhanced PHP-Nuke Web Portal System
 =======================================================================*/
 
 /************************************************************************
   Nuke-Evolution: Donations Xtreme module
   ============================================
   Copyright (c) 2010 by DarkForgeGfx.com

   Filename      : donations_ipn.php
   Author        : Travo DarkForgeGfx.com
   Version       : 1.0
   Date          : 26/03/10 (DD/MM/YY)
************************************************************************/

function get_microtime() {
    list($usec, $sec) = explode(' ', microtime());
    return ($usec + $sec);
}

function is_admin($trash=0) {
    static $adminstatus;
    if(isset($adminstatus)) return $adminstatus;
    $admincookie = isset($_COOKIE['admin']) ? $_COOKIE['admin'] : false;
    if (!$admincookie) { return $adminstatus = 0; }
    $admincookie = (!is_array($admincookie)) ? explode(':', base64_decode($admincookie)) : $admincookie;
    $aid = $admincookie[0];
    $pwd = $admincookie[1];
    $aid = substr(addslashes($aid), 0, 25);
    if (!empty($aid) && !empty($pwd)) {
        if (!function_exists('get_admin_field')) {
            global $db, $prefix;
            $pass = $db->sql_ufetchrow("SELECT `pwd` FROM `" . $prefix . "_authors` WHERE `aid` = '" .  str_replace("\'", "''", $aid) . "'", SQL_ASSOC);
            $pass = (isset($pass['pwd'])) ? $pass['pwd'] : '';
        } else {
            $pass = get_admin_field('pwd', $aid);
        }
        if ($pass == $pwd && !empty($pass)) {
            return $adminstatus = 1;
        }
    }
    return $adminstatus = 0;
}

define('NUKE_EVO', '2.0.0');
define('EVO_EDITION', 'xtreme');
define('PHPVERS', @phpversion());
define('EVO_VERSION', NUKE_EVO . ' ' . EVO_EDITION);

define('NUKE_BASE_DIR', dirname(__FILE__) . '/');
define('NUKE_BLOCKS_DIR', NUKE_BASE_DIR . 'blocks/');
define('NUKE_IMAGES_DIR', NUKE_BASE_DIR . 'images/');
define('NUKE_INCLUDE_DIR', NUKE_BASE_DIR . 'includes/');
define('NUKE_LANGUAGE_DIR', NUKE_BASE_DIR . 'language/');
define('NUKE_MODULES_DIR', NUKE_BASE_DIR . 'modules/');
define('NUKE_THEMES_DIR', NUKE_BASE_DIR . 'themes/');
define('NUKE_ADMIN_DIR', NUKE_BASE_DIR . 'admin/');
define('NUKE_RSS_DIR', NUKE_INCLUDE_DIR . 'rss/');
define('NUKE_DB_DIR', NUKE_INCLUDE_DIR . 'db/');
define('NUKE_ADMIN_MODULE_DIR', NUKE_ADMIN_DIR . 'modules/');
define('NUKE_FORUMS_DIR', (defined("IN_ADMIN") ? './../' : 'modules/Forums/'));
define('NUKE_CACHE_DIR', NUKE_INCLUDE_DIR . 'cache/');
define('NUKE_CLASSES_DIR', NUKE_INCLUDE_DIR . 'classes/');
define('NUKE_ZEND_DIR', NUKE_BASE_DIR . 'Zend/');
// define the INCLUDE PATH
define('INCLUDE_PATH', NUKE_BASE_DIR);

@require_once(NUKE_BASE_DIR.'config.php');
if(!$directory_mode) {
    $directory_mode = 0777;
} else {
    $directory_mode = 0755;
}
if (!$file_mode) {
    $file_mode = 0666;
} else {
    $file_mode = 0644;
}

require_once(NUKE_DB_DIR.'db.php');
require_once(NUKE_CLASSES_DIR.'class.cache.php');
require_once(NUKE_CLASSES_DIR.'class.debugger.php');
require_once(NUKE_INCLUDE_DIR.'functions_evo.php');
include_once(NUKE_INCLUDE_DIR.'validation.php');

$nukeconfig = load_nukeconfig();
foreach($nukeconfig as $var => $value) {
    $$var = $value;
}
$adminmail = stripslashes($adminmail);

require_once(dirname(__FILE__) . '/modules/Donations/ipn.php');