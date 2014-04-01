<?php
$loader = require ROOT . "/vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;

$app=new Silex\Application();
$api = Stack\lazy(function () {
    return require "Esnuab/Libro/Libro.php";
});
$cron = Stack\lazy(function () {
    return require "Esnuab/Cron/Cron.php";
});
$app = new Stack\UrlMap(
	$app,
	array(	
		"/api" => $api,
		"/cron" => $cron
    )
);
$request = Request::createFromGlobals();
$response = $app->handle($request);
$response->send();
