<?php
echo "work success";exit;
ini_set("display_errors", "On");
error_reporting(E_ALL^E_NOTICE);

define("DS", DIRECTORY_SEPARATOR);
define("DT", '.');

define("APP_PATH", realpath(dirname(__FILE__)));
define("APP_REAL_PATH", dirname(__FILE__));
define("APP_INI_PATH", APP_PATH. DS. 'conf'. DS. 'application.ini');

require_once APP_PATH. DS. 'application/library/Lib/Const/Basic.php';

$app = new Yaf_Application(APP_INI_PATH);
$app->bootstrap()->run();