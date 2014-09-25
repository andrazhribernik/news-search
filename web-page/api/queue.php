<?php

//error_reporting(E_ALL);
//ini_set('display_errors', '1');

// Direktorij kjer je aplikacija - ostala konfiguracija je v $basedir/etc/config.php
define("BASE_PATH", dirname(__FILE__)."/../");
define("LIB_PATH", dirname(__FILE__)."/../lib/");
define("LOG_PATH", dirname(__FILE__)."/../log/");

// Includes
require_once(LIB_PATH."log.php");
require_once(LIB_PATH."rest.php");

// UTF-8
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');
mb_http_input('UTF-8');
mb_language('uni');
mb_regex_encoding('UTF-8');
ob_start('mb_output_handler');

// Set time zone
date_default_timezone_set('Europe/Ljubljana');

// Logiranje
$log = new Log(LOG_PATH . date('Y-m-d') . basename(__FILE__));
$GLOBALS['log'] = $log;

// Allowed users
$GLOBALS['users'] = array("snap");

// Branje konfiguracije
$config = new Config(CONFIG_FILE);

$myApi = new REST($config);
$myApi->processAPI();

?>