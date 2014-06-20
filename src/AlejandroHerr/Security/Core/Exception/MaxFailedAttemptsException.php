<?php

namespace AlejandroHerr\Security\Core\Exception;

class MaxFailedAttemptsException extends WsseAuthenticationException
{
    public function __construct($message = 'Max failed attempts from this IP', \Exception $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
