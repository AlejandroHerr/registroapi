<?php

namespace AlejandroHerr\Security\Core\Exception;

class BadCredentialsException extends WsseAuthenticationException
{
    public function __construct($message = 'Bad Credentials', \Exception $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
