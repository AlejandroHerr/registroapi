<?php

namespace AlejandroHerr\Security\Core\Exception;

class WrongWsseHeadersException extends WsseAuthenticationException
{
    public function __construct($message = 'Wrong Wsse Headers', \Exception $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
