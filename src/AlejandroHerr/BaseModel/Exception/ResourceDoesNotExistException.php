<?php

namespace AlejandroHerr\BaseModel\Exception;

use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ResourceDoesNotExistException extends ResourceNotFoundException
{
    public function __construct($id)
    {
        parent::__construct(sprintf('El recurso %u no existe',$id),404);
    }
}
