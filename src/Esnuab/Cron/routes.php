<?php

use Esnuab\Cron\Controller\CronController;

$app->mount('/', new CronController($app['confirmation_manager'],$app['mandrill'],$app['mailchimp']));
