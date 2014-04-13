<?php
$app['security.encoder.digest'] = $app->share(function ($app) {
    return new MessageDigestPasswordEncoder('sha1', false, 1);
});
$app['security.cache']= ROOT . '/var/security_cache';
$app['security.authentication_listener.factory.wsse'] = $app->protect(function ($name, $options) use ($app) {
    $app['security.authentication_provider.' . $name . '.wsse'] = $app->share(function () use ($app) {
        return new \Esnuab\Libro\Security\WsseProvider($app['security.user_provider.default'], $app['security.cache']);
    });
    $app['security.authentication_listener.' . $name . '.wsse'] = $app->share(function () use ($app, $name) {
        return new \Esnuab\Libro\Security\WsseListener($app['security'], $app['security.authentication_provider.' . $name . '.wsse'], $app['db'],$app['monolog.access']);
    });

    return array(
        'security.authentication_provider.' . $name . '.wsse',
        'security.authentication_listener.' . $name . '.wsse',
        null,
        'pre_auth'
    );
});
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'default' => array(
            'pattern' => "/",
            'wsse' => true,
            'stateless' => true,
            'users' => $app->share(function () use ($app) {
                return new \Esnuab\Libro\Security\UserProvider($app['db']);
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
