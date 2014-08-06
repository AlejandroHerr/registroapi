<?php

namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ServiceProviderInterface;

class SocioControllerProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    public function boot(Application $app)
    {
    }

    public function register(Application $app)
    {
        $app['libro.socio_controller'] = $app->share(function ($app) {
            return new SocioController(
                $app['libro.socio_controller.manager'],
                $app['libro.socio_controller.entity'],
                $app['libro.socio_controller.form'],
                isset($app['libro.socio_controller.logger']) ? $app['libro.socio_controller.logger'] : null,
                isset($app['libro.task_scheduler']) ? $app['libro.task_scheduler'] : null
            );
        });
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('','libro.socio_controller:getCollection')
            ->before($app['filter.only_admin'])
            ->before('libro.socio_controller:getQueryHeaders');
        $controllers->post('','libro.socio_controller:postResource')
            ->before($app['filter.only_user'])
            ->before('libro.socio_controller:getFormHeaders');
        $controllers->delete('/{id}','libro.socio_controller:deleteResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin']);
        $controllers->get('/{id}','libro.socio_controller:getResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_admin']);
        $controllers->match('/{id}','libro.socio_controller:patchResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before('libro.socio_controller:getFormHeaders')
            ->method('PATCH');
        $controllers->put('/{id}','libro.socio_controller:putResource')
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before('libro.socio_controller:getFormHeaders');

        return $controllers;
    }
}
