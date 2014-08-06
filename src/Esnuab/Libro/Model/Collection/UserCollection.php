<?php

namespace Esnuab\Libro\Model\Collection;

use AlejandroHerr\BaseModel\Collection\AbstractCollection;

class UserCollection extends AbstractCollection
{
    public function __construct($objects = array())
    {
        parent::__construct('Esnuab\Libro\Model\Entity\User', $objects);
    }
}
