<?php
$loader = require ROOT . "/vendor/autoload.php";

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Silex\Provider\SwiftmailerServiceProvider;


$app = new Silex\Application();

$app['debug'] = true;

$app['db.config'] = require_once 'db.php';

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
$app->GET("/hola",function(){
	return "hola";
});
require_once 'routes.php';

$app['transaction_logger'] = $app->share(function() use ($app){
	return new Esnuab\Services\TransactionLogger\TransactionLogger();
});
/*
$app->finish(function (Request $request, JsonResponse $jsonResponse) use ($app) {
	if(in_array($request->getMethod(),array('POST','PUT','DELETE')) && in_array($jsonResponse->getStatusCode(),array(201,204))){
		$app['transaction_logger']->setRequest($request);
		if($request->getMethod() == 'DELETE'){
			$app['transaction_logger']->setTargetByUri();
		}
		if(in_array($request->getMethod(),array('POST','PUT'))){
			$app['transaction_logger']->setData($jsonResponse->getContent());
			$app['transaction_logger']->setTargetByResponse();
		}
		$app['transaction_logger']->setUser($app['security']->getToken()->getUsername());
		$app['transaction_logger']->buildLog();
		$app['transaction_logger']->recordLog($app['db']);
	}
});*/

/*$app->register(new Silex\Provider\SwiftmailerServiceProvider());
$app['swiftmailer.options'] = require_once 'mailer.php';*/
return $app;
