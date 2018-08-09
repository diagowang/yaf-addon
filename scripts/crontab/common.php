<?php
error_reporting(E_ERROR);
define('APP_PATH', realpath(dirname(dirname(__DIR__))));
$app = new Yaf_Application(APP_PATH . '/conf/app.ini');
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$app->bootstrap()->getDispatcher()->dispatch(new Yaf_Request_Simple());
