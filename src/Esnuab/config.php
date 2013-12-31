<?php
//define('ROOT',dirname(dirname(__DIR__)));


$loader = require ROOT . "/vendor/autoload.php";
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\HttpFoundation\Request;


require_once 'db.php';

$app = new Silex\Application();

$app['debug'] = true;



$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), $configDB);



//*********************
//** ENTITY MANAGERS **
//*********************

$app['socio_manager'] = $app->share(function($app) {
	return new \Esnuab\Libro\Model\Manager\SocioManager($app['db']);
});
$app['historia_manager'] = $app->share(function($app) {
	return new \Esnuab\Libro\Model\Manager\HistoriaManager($app['db']);
});


//*********************
//**     SECURITY    **
//*********************
require_once 'security.php';

foreach (array('user','admin','superadmin') as $role) {
	$app['filter.only_' . $role] = $app->protect(function(Request $request) use ($role,$app) {
		if (!$app['security']->isGranted('ROLE_' . strtoupper($role))) {
			$message = "No tienes permiso para acceder.";
			
			$app['loggr']->addLog('Instento de entrada sin permiso', $request/*,$app['security']->getToken()->getUser()->getUsername()*/);
			return $app->json("No tienes permiso para acceder.",403);
		}
	});
}




//*********************
//**     ROUTING     **
//*********************

require_once 'routes.php';

return $app;
