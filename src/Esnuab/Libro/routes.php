<?php

use Esnuab\Libro\Controller\AdminController;
use Esnuab\Libro\Controller\SocioController;

$app->mount('/admin', new AdminController($app['user_manager'],$app['monolog.transaction']));
$app->mount('/', new SocioController($app['socio_manager'],$app['monolog.transaction']));
