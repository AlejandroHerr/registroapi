<?php

use Esnuab\Libro\Controller\ApiControllerProvider;

$app->mount('/api', new Esnuab\Libro\Controller\ApiController($app['socio_manager']));

$app->get('/', function() {
    return "hola";
});