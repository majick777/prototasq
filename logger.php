<?php

/* Super Simple Console Logger */

if (!isset($_REQUEST['log'])) {exit;}
$log = $_REQUEST['log'];
if ($log == '') {exit;}
$log = json_decode($log, true);

$config = dirname(__FILE__).'/config.php';
if (file_exists($config)) {include($config);}

if (defined('PT_LOG_FILEPATH')) {$logpath = PT_LOG_FILEPATH;}
else {$logpath = dirname(__FILE__).'/console.log';}

if ( (is_array($log)) || (is_object($log)) ) {$log = print_r($log, true);}

error_log($log.PHP_EOL, 3, $logpath);
