<?php
$loader = require ROOT . "/vendor/autoload.php";

use Silex\Application;
use Stack\LazyHttpKernel;
use AlejandroHerr\Stack\Cors;
use Symfony\Component\HttpFoundation\Request;

$app=new Application();

$api = new LazyHttpKernel(function () {
    return require 'Esnuab/Libro/Libro.php';
});
$corsCfg = require ROOT . '/config/cors.php';
$api = new Cors($api,$corsCfg);

$cron = new LazyHttpKernel(function () {
    return require 'Esnuab/Cron/Cron.php';
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
