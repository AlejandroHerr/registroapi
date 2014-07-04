<?php
define('ROOT',dirname(dirname(__FILE__)));
date_default_timezone_set('Europe/Madrid');
function printArray($arrayname=array())
{
    echo "<pre>";
    print_r($arrayname);
    echo '</pre>';
}
function dumpArray($arrayname=array())
{
    echo "<pre>";
    var_dump($arrayname);
    echo '</pre>';
}

require_once ROOT.'/src/config.php';
