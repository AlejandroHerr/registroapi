<?php

namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

class LogControllerProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    const ASSERT_DATE = '[2][0-9]{3}-[0-1][0-9]-[0-3][0-9]';

    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['libro.log_controller'] = $app->share(function ($app) {
            return new LogController(
                $app['libro.log_controller.manager'],
                isset($app['libro.log_controller.logger']) ? $app['libro.log_controller.logger'] : null
            );
        });
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('', "libro.log_controller:getLogs");
        $controllers->get('/{date}/{type}.{ext}', "libro.log_controller:getResource")
            ->assert('date',self::ASSERT_DATE)
            ->assert('type','\w+')
            ->assert('ext','\w+');
        $controllers->get('/{date}/{type}', "libro.log_controller:getResource")
            ->assert('date',self::ASSERT_DATE)
            ->assert('type','\w+');
        $controllers->get('/{date}', "libro.log_controller:getLogByDate")
            ->assert('date',self::ASSERT_DATE);
        $controllers->get('/', "libro.log_controller:getLogs");
        $controllers->get('/{date}', "libro.log_controller:getLog")
            ->assert('date',self::ASSERT_DATE);
        $controllers->get('/{date1}/{date2}/', "libro.log_controller:getRange")
            ->assert('date1',self::ASSERT_DATE)
            ->assert('date2',self::ASSERT_DATE);

        return $controllers;
    }
}
