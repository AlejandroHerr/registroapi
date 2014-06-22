<?php

namespace AlejandroHerr\ApiApplication\Model\Exception;

class DuplicatedValueException extends ConflictException
{
    public function __construct($field)
    {
        parent::__construct(sprintf('El valor del campo %s ya existe',$field));
    }
}
