<?php

namespace AlejandroHerr\Security\Core\Exception;

class NonceExpiredException extends WsseAuthenticationException
{
    public function __construct($message = 'Nonce previously used', \Exception $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
