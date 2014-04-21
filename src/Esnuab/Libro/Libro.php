<?php

use Symfony\Component\HttpFoundation\Request;

$app = new Silex\Application();
$app['debug'] = true;
//DB
$app['db.config'] = require_once ROOT . '/config/db.php';
$app->register(new Silex\Provider\DoctrineServiceProvider(),$app['db.config']);
$app['socio_manager'] = $app->share(function ($app) {
    return new \Esnuab\Libro\Model\Manager\SocioManager($app['db']);
});
//MONOLOG
$app->register(new Silex\Provider\MonologServiceProvider());
$app['monolog.logfile']=function () {
    $date = \DateTime::createFromFormat('U',time());
    $file = ROOT.'/var/logs/app_'.$date->format('Y-m-d').'.log';

    return $file;
};
$app['monolog.factory'] = $app->protect(function ($name) use ($app) {
    $log = new $app['monolog.logger.class']($name);

    return $log;
});
foreach (array('access','transaction') as $channel) {
    $app['monolog.'.$channel] = $app->share(function () use ($app,$channel) {
            $log = new $app['monolog.logger.class']($channel);
            $handler = new Esnuab\AuditLog\Handler\DbalHandler($app['db']);
            $handler->setFormatter(new Esnuab\AuditLog\Formatter\AuditFormatter());
            $handler->pushProcessor(new Esnuab\AuditLog\Processor\RequestProcessor($app));
            $handler->pushProcessor(new Esnuab\AuditLog\Processor\UserProcessor($app));
            $log->pushHandler($handler);

            return $log;
        });
}
//SECURITY
require_once 'security.php';
foreach (array('user','admin','superadmin') as $role) {
    $app['filter.only_' . $role] = $app->protect(function (Request $request) use ($role,$app) {
        if (!$app['security']->isGranted('ROLE_' . strtoupper($role))) {
            $message = "No tienes permiso para acceder.";
            if (null != $app['monolog.access']) {
                $app['monolog.access']->addNotice('Acceso prohibido');
            }

            return $app->json("No tienes permiso para acceder.",403);
        }
    });
}
//ROUTING

require_once 'routes.php';
$app->match('/socios', function () use ($app) {
    return $app->json(array('hola'), 201,array('Access-Control-Allow-Origin' => 'http://app.localhost','Access-Control-Allow-Headers'=>'X-WSSE'));
})->method('OPTIONS');
return $app;
