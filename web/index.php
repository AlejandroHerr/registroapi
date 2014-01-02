<?php
define('ROOT',dirname(dirname(__FILE__)));
date_default_timezone_set('UTC');
$app = require_once ROOT.'/src/Esnuab/config.php';
$app->run();

