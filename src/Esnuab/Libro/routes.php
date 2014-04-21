<?php

use Esnuab\Libro\Controller\ApiController;

$app->mount('/', new ApiController($app['socio_manager'],$app['monolog.transaction']));
