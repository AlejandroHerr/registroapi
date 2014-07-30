<?php

use AlejandroHerr\ApiApplication\JsonExceptionHandler;
use AlejandroHerr\AuditLog\Formatter\AuditFormatter;
use AlejandroHerr\AuditLog\Handler\DbalHandler;
use AlejandroHerr\AuditLog\Processor\RequestProcessor;
use AlejandroHerr\AuditLog\Processor\UserProcessor;
use Esnuab\Libro\Model\Manager\SocioManager;
use Esnuab\Libro\Model\Manager\UserManager;
use Esnuab\Libro\Services\CronTaskScheduler\CronTaskScheduler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

$app = new Application();

$app['debug'] = true;

######################
# exception handlers #
######################

$app['exception_handler'] = $app->share(function () use ($app) {
    return new JsonExceptionHandler($app['debug']);
});

$app->error(function (\RuntimeException $e, $code) {
    if ($e->getCode() >= 500 || $e->getCode() < 400) {
        return;
    }

    $message = array('message' => $e->getMessage());

    return new JsonResponse($message,$e->getCode());
});

###################
# services        #
###################

$app['db.config'] = require_once ROOT . '/config/db.php';
$app->register(new DoctrineServiceProvider(),$app['db.config']);

###################
# loggers         #
###################
$app->register(new MonologServiceProvider());
$app['monolog.name'] = 'main';
$app['monolog.path'] = function () {
    return ROOT.'/var/logs';
};
$app['monolog.logfile']= function () use ($app) {
    $date = \DateTime::createFromFormat('U',time());
    $file = $app['monolog.path'] . '/app_'.$date->format('Y-m-d').'.log';

    return $file;
};
$app['monolog'] = $app->share($app->extend('monolog', function ($monolog, $app) {
    $handlers = $monolog->getHandlers();
    $handlers[0]->setFormatter(new JsonFormatter());

    return $monolog;
}));
$app['monolog.factory'] = $app->protect(function ($name) use ($app) {
    $log = new $app['monolog.logger.class']($name);
    $handler = new StreamHandler($app['monolog.logfile']);
    $handler->setFormatter(new JsonFormatter());
    $handler->pushProcessor(new RequestProcessor($app));
    $handler->pushProcessor(new UserProcessor($app));
    $log->pushHandler($handler);

    return $log;
});

###################
# model managers  #
###################

$app['monolog.access'] = $app->share(function ($app) {
    return $app['monolog.factory']('access');
});
$app['monolog.transaction'] = $app->share(function () use ($app) {

    $log =  $app['monolog.factory']('transaction');
    $handler = new DbalHandler($app['db'],Logger::NOTICE);
    $handler->setFormatter(new AuditFormatter());
    $handler->pushProcessor(new RequestProcessor($app));
    $handler->pushProcessor(new UserProcessor($app));
    $log->pushHandler($handler);

    return $log;
});

###################
# model managers  #
###################
$app['socio_manager'] = $app->share(function ($app) {
    return new SocioManager($app['db'],$app['monolog.transaction']);
});
$app['user_manager'] = $app->share(function ($app) {
    return new UserManager($app['db'],$app['monolog.transaction']);
});
$app['task_scheduler'] = $app->share(function ($app) {
    return new CronTaskScheduler($app['db']);
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
