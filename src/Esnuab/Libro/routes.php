<?php

use Esnuab\Libro\Controller\LogControllerProvider;
use Esnuab\Libro\Controller\SocioControllerProvider;
use Esnuab\Libro\Controller\UserControllerProvider;
use Silex\Provider\ServiceControllerServiceProvider;

$app->register(new ServiceControllerServiceProvider());

$logControllerProvider = new LogControllerProvider;
$app->register($logControllerProvider);
$app->mount('/log', $logControllerProvider);

$socioControllerProvider = new SocioControllerProvider;
$app->register($socioControllerProvider, array(
    'libro.socio_controller.entity' => 'Esnuab\Libro\Model\Entity\Socio',
    'libro.socio_controller.form' => 'Esnuab\Libro\Form\SocioForm'
));
$app->mount('/socio', $socioControllerProvider);

$userControllerProvider = new UserControllerProvider;
$app->register($userControllerProvider, array(
    'libro.user_controller.entity' => 'Esnuab\Libro\Model\Entity\User',
    'libro.user_controller.form' => 'Esnuab\Libro\Form\UserForm'
));
$app->mount('/user', $userControllerProvider);
