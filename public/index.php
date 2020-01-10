<?php
define("APP_PATH",  realpath(dirname(__FILE__) . '/../'));
date_default_timezone_set('Asia/Shanghai');
$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");
$app->bootstrap()->run();
