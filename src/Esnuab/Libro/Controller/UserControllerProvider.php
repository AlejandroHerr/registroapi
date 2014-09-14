<?php

namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

class UserControllerProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['libro.user_controller'] = $app->share(function ($app) {
            return new UserController(
                $app['libro.user_controller.manager'],
                $app['libro.user_controller.entity'],
                $app['libro.user_controller.form'],
                isset($app['libro.user_controller.logger']) ? $app['libro.user_controller.logger'] : null,
                isset($app['libro.task_scheduler']) ? $app['libro.task_scheduler'] : null
            );
        });
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('', 'libro.user_controller:getCollection')
            ->before('libro.user_controller:getQueryHeaders');
        $controllers->post('', 'libro.user_controller:postResource')
            ->before($app['filter.only_superadmin'])
            ->before('libro.user_controller:getFormHeaders');
        $controllers->delete('/{id}', 'libro.user_controller:deleteResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin']);
        $controllers->get('/{id}', 'libro.user_controller:getResource')
            ->assert('id', '\d+');
        $controllers->match('/{id}', 'libro.user_controller:patchResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before('libro.user_controller:getFormHeaders')
            ->method('PATCH');
        $controllers->put('/{id}', 'libro.user_controller:putResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before(array($this,'getFormHeaders'));
        $controllers->before($app['filter.only_admin']);

        return $controllers;
    }
}
