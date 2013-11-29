<?php
//define('ROOT',dirname(dirname(__DIR__)));


$loader = require ROOT . "/vendor/autoload.php";
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
require_once 'db.php';

$app = new Silex\Application();

$app['debug'] = true;



$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), $configDB);


$app['socio_manager'] = $app->share(function($app) {
    return new \Esnuab\Libro\Model\Manager\SocioManager($app['db']);
});
$app['historia_manager'] = $app->share(function($app) {
    return new \Esnuab\Libro\Model\Manager\HistoriaManager($app['db']);
});


              /***************************/
             /****/                 /****/
            /****/  /***********/  /****/
           /****/  /**ROUTING**/  /****/
          /****/  /***********/  /****/
         /****/  /**ROUTING**/  /****/
        /****/  /***********/  /****/
       /****/  /**ROUTING**/  /****/
      /****/  /***********/  /****/
     /****/  /**ROUTING**/  /****/
    /****/  /***********/  /****/
   /****/  /**ROUTING**/  /****/
  /****/  /***********/  /****/
 /****/                 /****/
/***************************/
///if(!$app['debug']){
  require_once 'security.php';
//}
require_once 'routes.php';





return $app;
