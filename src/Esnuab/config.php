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
    return new \Esnuab\Libro\Model\SocioManager($app['db']);
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
require_once 'security.php';
require_once 'routes.php';





return $app;
