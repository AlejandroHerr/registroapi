<?php

namespace AlejandroHerr\Security\Core\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class WsseAuthenticationException extends AuthenticationException
{
    public function __construct($message = 'Authentication Failed', \Exception $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}
