<?php

use AlejandroHerr\Security\Core\Authentication\Provider\WsseProvider;
use AlejandroHerr\Security\Core\User\UserProvider;
use AlejandroHerr\Security\Http\Firewall\WsseExceptionListener;
use AlejandroHerr\Security\Http\Firewall\WsseListener;
use Silex\Provider\SecurityServiceProvider;

$app['security.cache']= ROOT . '/var/security_cache';
$app['security.authentication_listener.factory.wsse'] = $app->protect(function ($name, $options) use ($app) {
    $app['security.authentication_provider.' . $name . '.wsse'] = $app->share(function () use ($app) {
        return new WsseProvider($app['security.user_provider.default'], $app['security.cache']);
    });
    $app['security.authentication_listener.' . $name . '.wsse'] = $app->share(function () use ($app, $name) {
        return new WsseListener($app['security'], $app['security.authentication_provider.' . $name . '.wsse'], $app['db'],$app['monolog.access']);
    });
    $app['security.exception_listener.'.$name] = $app->share(function () use ($app, $name) {
        return new WsseExceptionListener(
            $app['security'],
            $app['security.trust_resolver'],
            $app['security.http_utils'],
            $name,
            null,
            null,
            null,
            $app['monolog']
        );
    });

    return array(
        'security.authentication_provider.' . $name . '.wsse',
        'security.authentication_listener.' . $name . '.wsse',
        null,
        'pre_auth'
    );
});

$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => "/",
            'wsse' => true,
            'stateless' => true,
            'users' => $app->share(function () use ($app) {
                return new UserProvider($app['db']);
            })
        )
    )
));
$app['security.role_hierarchy'] = array(
    'ROLE_USER' => array(
        'ROLE_USER'
    ),
    'ROLE_ADMIN' => array(
        'ROLE_USER',
        'ROLE_ALLOWED_TO_SWITCH'
    ),
    'ROLE_SUPERADMIN' => array(
        'ROLE_ADMIN'
    ),
    'ROLE_COLABORADOR' => array(
        'ROLE_USER'
    ),
    'ROLE_JUNTA' => array(
        'ROLE_ADMIN'
    ),
    'ROLE_SECRETARIO' => array(
        'ROLE_SUPERADMIN'
    ),
    'ROLE_PRESIDENTE' => array(
        'ROLE_SUPERADMIN'
    )
);
