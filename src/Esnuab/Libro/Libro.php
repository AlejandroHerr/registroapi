<?php

use AlejandroHerr\ApiApplication\JsonExceptionHandler;
use AlejandroHerr\AuditLog\Formatter\AuditFormatter;
use AlejandroHerr\AuditLog\Handler\DbalHandler;
use AlejandroHerr\AuditLog\Processor\RequestProcessor;
use AlejandroHerr\AuditLog\Processor\UserProcessor;
use Esnuab\Libro\Model\Manager\SocioManager;
use Esnuab\Libro\Model\Manager\UserManager;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

$app['exception_handler'] = $app->share(function () use ($app) {
    return new JsonExceptionHandler($app['debug']);
});
$app['debug'] = true;

$app['db.config'] = require_once ROOT . '/config/db.php';
$app->register(new DoctrineServiceProvider(),$app['db.config']);

$app->register(new MonologServiceProvider());
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
            $handler = new DbalHandler($app['db']);
            $handler->setFormatter(new AuditFormatter());
            $handler->pushProcessor(new RequestProcessor($app));
            $handler->pushProcessor(new UserProcessor($app));
            $log->pushHandler($handler);

            return $log;
        });
}

###################
# model managers  #
###################
$app['socio_manager'] = $app->share(function ($app) {
    return new SocioManager($app['db']);
});
$app['user_manager'] = $app->share(function ($app) {
    return new UserManager($app['db']);
});

###################
# security        #
###################
require_once 'security.php';
foreach (array('user','admin','superadmin') as $role) {
    $app['filter.only_' . $role] = $app->protect(function (Request $request) use ($role,$app) {
        if (!$app['security']->isGranted('ROLE_' . strtoupper($role))) {
            $message = "No tienes permiso para acceder.";
            if (null != $app['monolog.access']) {
                $app['monolog.access']->addNotice('Acceso prohibido');
            }

            return $app->json("No tienes permiso para acceder.",403,$app['cors.headers']);
        }
    });
}

##################
# routing        #
##################
require_once 'routes.php';

return $app;
