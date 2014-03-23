<?php
$loader = require ROOT . "/vendor/autoload.php";

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;

//DB
$app['db.config'] = require_once 'config/db.php';
$app->register(new Silex\Provider\DoctrineServiceProvider(),$app['db.config']);
$app['socio_manager'] = $app->share(function($app) {
	return new \Esnuab\Libro\Model\Manager\SocioManager($app['db']);
});
$app['confirmation_manager'] = $app->share(function($app) {
	return new \Esnuab\Libro\Model\Manager\ConfirmationManager($app['db']);
});
$app['mandrill.apikey'] = require_once 'config/mandrill.php';
$app['mandrill'] = $app->share(function($app) {
	return new Mandrill($app['mandrill.apikey']);
});
list($app['mailchimp.apikey'],$app['mailchimp.listid']) = require_once 'config/mailchimp.php';
$app['mailchimp'] = $app->share(function($app) {
	return new Mailchimp($app['mailchimp.apikey']);
});
//MONOLOG
$app->register(new Silex\Provider\MonologServiceProvider());
$app['monolog.logfile']=function(){
	$date = \DateTime::createFromFormat('U',time());
	$file = ROOT.'/var/logs/app_'.$date->format('Y-m-d').'.log';
	return $file;
};
$app['monolog.factory'] = $app->protect(function ($name) use ($app) {
	$log = new $app['monolog.logger.class']($name);
	return $log;
});
foreach (array('access','transaction') as $channel) {
	$app['monolog.'.$channel] = $app->share(function() use ($app,$channel){
			$log = new $app['monolog.logger.class']($channel);
			$handler = new Esnuab\Services\AuditLog\Handler\DbalHandler($app['db']);
			$handler->setFormatter(new Esnuab\Services\AuditLog\Formatter\AuditFormatter());
			$handler->pushProcessor(new Esnuab\Services\AuditLog\Processor\RequestProcessor($app));
			$handler->pushProcessor(new Esnuab\Services\AuditLog\Processor\UserProcessor($app));
			$log->pushHandler($handler);
			return $log;
		});
}

//SECURITY
require_once 'security.php';
foreach (array('user','admin','superadmin') as $role) {
	$app['filter.only_' . $role] = $app->protect(function(Request $request) use ($role,$app) {
		if (!$app['security']->isGranted('ROLE_' . strtoupper($role))) {
			$message = "No tienes permiso para acceder.";
			if( null != $app['monolog.access']){
				$app['monolog.access']->addNotice('Acceso prohibido');
			}
			return $app->json("No tienes permiso para acceder.",403);
		}
	});
}

//ROUTING
require_once 'routes.php';

return $app;
