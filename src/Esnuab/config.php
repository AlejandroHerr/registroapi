<?php
$loader = require ROOT . "/vendor/autoload.php";

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Silex\Provider\SwiftmailerServiceProvider;


$app = new Silex\Application();

// list($usec, $sec) = explode(" ", microtime());
// $time0 = ((float)$usec + (float)$sec);
	 

// $t7=$time0;

$app['debug'] = true;

$app['db.config'] = require_once 'config/db.php';

$app->register(new Silex\Provider\DoctrineServiceProvider(),$app['db.config']);


//*********************
//** ENTITY MANAGERS **
//*********************

$app['socio_manager'] = $app->share(function($app) {
	return new \Esnuab\Libro\Model\Manager\SocioManager($app['db']);
});


//*********************
//**     SECURITY    **
//*********************
require_once 'security.php';

foreach (array('user','admin','superadmin') as $role) {
	$app['filter.only_' . $role] = $app->protect(function(Request $request) use ($role,$app) {
		if (!$app['security']->isGranted('ROLE_' . strtoupper($role))) {
			$message = "No tienes permiso para acceder.";
			
			//$app['loggr']->addLog('Instento de entrada sin permiso', $request/*,$app['security']->getToken()->getUser()->getUsername()*/);
			return $app->json("No tienes permiso para acceder.",403);
		}
	});
}



//*********************
//**     ROUTING     **
//*********************

require_once 'routes.php';

$app['monolog.logfile']=function(){
	$date = \DateTime::createFromFormat('U',time());
	$file = ROOT.'/var/logs/app_'.$date->format('Y-m-d').'.log';
	return $file;
};
$app->register(new Silex\Provider\MonologServiceProvider());

$app['monolog.factory'] = $app->protect(function ($name) use ($app) {
	$log = new $app['monolog.logger.class']($name);
	//$log->pushHandler($app['monolog.handler']);

	return $log;
});
$app['monolog.tiempo'] = $app->share(function($app){
	$log = new $app['monolog.logger.class']('tiempo');
	return $log;
});

$app['monolog.access'] = $app->share(function() use ($app){
		$log = new $app['monolog.logger.class']('access');
		$handler = new Esnuab\Services\DbalLog\Handler\DbalHandler($app['db']);
		$handler->setFormatter(new Esnuab\Services\DbalLog\Formatter\AuditFormatter());
		$handler->pushProcessor(new Esnuab\Services\DbalLog\Processor\RequestProcessor($app));
		$handler->pushProcessor(new Esnuab\Services\DbalLog\Processor\UserProcessor($app));
		$log->pushHandler($handler);
		return $log;
	});
$app->before(function() use ($app){

	
	//$app['monolog.access']->addInfo('hola');
});
$app->after(function() use ($app){ 
	
	$app['monolog.access']->addInfo('perrro',array('hola'=>'jaajaja'));
});

// 	$app['monolog']->addInfo($t7,array("tiempo0"));
// $app->before(function() use ($app){
// 	list($usec, $sec) = explode(" ", microtime());
// 	$time0 = ((float)$usec + (float)$sec);
// 	$t1=$time0;
// 	$app['monolog']->addInfo($t1,array("principio app"));
// }, Silex\Application::EARLY_EVENT);
// $app->finish(function(Request $request,JsonResponse $jsonresponse) use ($app){
// 	list($usec, $sec) = explode(" ", microtime());
// 	$time0 = ((float)$usec + (float)$sec);
// 	$t1=$time0;
// 	$app['monolog']->addInfo($t1,array("pre fin app"));
// 	$code = $jsonresponse->getStatusCode();
// 	switch ($code) {
// 		case 401:
// 			$app['monolog.access']->addNotice('No autenticado');
// 			break;
// 		case 403:
// 			$app['monolog.access']->addNotice('Sin autorizacion');
// 			break;
// 		default:
// 			$app['monolog.access']->addInfo('Acceso Correcto',array('user' => $app['security']->getToken()->getUsername()));
// 			break;
// 	}
// 	list($usec, $sec) = explode(" ", microtime());
// 	$time0 = ((float)$usec + (float)$sec);
// 	$t1=$time0;
// 	$app['monolog']->addInfo($t1,array("fin app"));
// });

return $app;
