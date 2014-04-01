<?php

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

//DB
$app['db.config'] = require_once ROOT . '/config/db.php';
$app->register(new Silex\Provider\DoctrineServiceProvider(),$app['db.config']);
$app['confirmation_manager'] = $app->share(function($app) {
	return new \Esnuab\Cron\Model\Manager\ConfirmationManager($app['db']);
});
$app['mandrill.apikey'] = require_once ROOT . '/config/mandrill.php';
$app['mandrill'] = $app->share(function($app) {
	return new Mandrill($app['mandrill.apikey']);
});
list($app['mailchimp.apikey'],$app['mailchimp.listid']) = require_once ROOT . '/config/mailchimp.php';
$app['mailchimp'] = $app->share(function($app) {
	return new Drewm\MailChimp($app['mailchimp.apikey']);
});
require_once 'routes.php';

return $app;
