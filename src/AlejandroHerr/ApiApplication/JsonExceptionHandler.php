<?php

namespace AlejandroHerr\ApiApplication;

use Silex\ExceptionHandler;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class JsonExceptionHandler extends ExceptionHandler
{
    public function onSilexError(GetResponseForExceptionEvent $event)
    {
        if (!$this->enabled) {
            return;
        }
        $handler = new Debug\JsonExceptionHandler($this->debug);
        $event->setResponse($handler->createResponse($event->getException(),$event->getRequest()));
    }
}
