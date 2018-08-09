<?php
error_reporting(E_ALL & ~E_NOTICE);
define('APPLICATION_PATH',  dirname(dirname(__FILE__)));
include APPLICATION_PATH . '/vendor/autoload.php';
require APPLICATION_PATH . '/conf/constant.php';
$app = new Yaf_Application(APPLICATION_PATH . '/conf/application.ini', ini_get('yaf.environ'));
$app->bootstrap() //call bootstrap methods defined in Bootstrap.php
    ->run();
