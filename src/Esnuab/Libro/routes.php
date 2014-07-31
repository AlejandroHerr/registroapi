<?php

use Esnuab\Libro\Controller\UserController;
use Esnuab\Libro\Controller\LogController;
use Esnuab\Libro\Controller\SocioController;

$app->mount('/user', new UserController($app['user_manager'],null,$app['task_scheduler']));
$app->mount('/logs', new LogController($app['monolog.path']));
$app->mount('/socio', new SocioController($app['socio_manager'],null,$app['task_scheduler']));
