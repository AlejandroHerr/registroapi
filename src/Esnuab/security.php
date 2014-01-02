<?php

/*
$app['security.role_hierarchy'] = array(
    'ROLE_USER' => array('ROLE_USER'),
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
    'ROLE_SUPERADMIN' => array('ROLE_ADMIN'),
    'ROLE_COLABORADOR' => array('ROLE_USER'),
    'ROLE_JUNTA' => array('ROLE_ADMIN'),
    'ROLE_SECRETARIO' => array('ROLE_SUPERADMIN'),
    'ROLE_PRESIDENTE' => array('ROLE_SUPERADMIN'),

);*/

$app['security.encoder.digest'] = $app->share(function ($app) {
    // use the sha1 algorithm
    // don't base64 encode the password
    // use only 1 iteration
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});

$app['security.authentication_listener.factory.wsse'] = $app->protect(function ($name, $options) use ($app) {
    // define the authentication provider object
    $app['security.authentication_provider.'.$name.'.wsse'] = $app->share(function () use ($app) {
        return new \Esnuab\Libro\Security\WsseProvider($app['security.user_provider.default'], __DIR__.'/security_cache');
    });

    // define the authentication listener object
    $app['security.authentication_listener.'.$name.'.wsse'] = $app->share(function () use ($app,$name) {
        return new \Esnuab\Libro\Security\WsseListener($app['security'], $app['security.authentication_provider.'.$name.'.wsse']);
    });

    return array(
        // the authentication provider id
        'security.authentication_provider.'.$name.'.wsse',
        // the authentication listener id
        'security.authentication_listener.'.$name.'.wsse',
        // the entry point id
        null,
        // the position of the listener in the stack
        'pre_auth'
    );
});

$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => "/api",
            'wsse' => true,
            'stateless' => true,
            'users' => $app->share(function () use ($app) {
                return new \Esnuab\Libro\Security\UserProvider($app['db']);
            }),
        ),
    ),
));
$app['security.role_hierarchy'] = array(
    'ROLE_USER' => array('ROLE_USER'),
    'ROLE_ADMIN' => array('ROLE_USER', 'ROLE_ALLOWED_TO_SWITCH'),
    'ROLE_SUPERADMIN' => array('ROLE_ADMIN'),
    'ROLE_COLABORADOR' => array('ROLE_USER'),
    'ROLE_JUNTA' => array('ROLE_ADMIN'),
    'ROLE_SECRETARIO' => array('ROLE_SUPERADMIN'),
    'ROLE_PRESIDENTE' => array('ROLE_SUPERADMIN'),
);