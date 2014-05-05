<?php

$app->mount('/admin', new Esnuab\Libro\Controller\AdminController($app['user_manager'],$app['monolog.transaction']));
$app->mount('/', new Esnuab\Libro\Controller\SocioController($app['socio_manager'],$app['monolog.transaction']));
