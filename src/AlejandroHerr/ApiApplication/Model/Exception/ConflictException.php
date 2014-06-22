<?php

namespace AlejandroHerr\ApiApplication\Model\Exception;

abstract class ConflictException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message,409);
    }
}
