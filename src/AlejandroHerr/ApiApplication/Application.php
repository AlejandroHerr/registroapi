<?php

namespace AlejandroHerr\ApiApplication;

use Silex\Application as App;

class Application extends App
{
    public function __construct(array $values = array())
    {
        parent::__construct();

        $app = $this;

        $this['exception_handler'] = $this->share(function () use ($app) {
            return new JsonExceptionHandler($app['debug']);
        });
    }
}
