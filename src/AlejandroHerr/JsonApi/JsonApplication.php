<?php

namespace AlejandroHerr\JsonApi;

use Silex\Application;

class JsonApplication extends Application
{
    public function __construct(array $values = array())
    {
        parent::__construct($values);

        $app = $this;

        $this['exception_handler'] = $this->share(function () use ($app) {
            return new JsonExceptionHandler($app['debug']);
        });
    }
}
