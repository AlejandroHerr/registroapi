<?php

use Esnuab\Libro\Controller\ApiController;
use Esnuab\Libro\Controller\CronController;

$app->mount('/api', new ApiController($app['socio_manager'],$app['monolog.transaction']));
$app->mount('/cron', new CronController($app['confirmation_manager'],$app['mandrill'],$app['mailchimp']));
