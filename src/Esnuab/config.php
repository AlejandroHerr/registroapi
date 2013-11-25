<?php

//define('ROOT',dirname(dirname(__DIR__)));

$loader = require ROOT . "/vendor/autoload.php";


$app = new Silex\Application();

$app['debug'] = true;

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'host' => '',
        'dbname' => '',
        'user' => '',
        'password' => '',
        'charset' => 'utf8'
    )
));

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

require_once 'routes.php';

return $app;
