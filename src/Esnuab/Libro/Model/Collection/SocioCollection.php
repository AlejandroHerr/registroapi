<?php

namespace Esnuab\Libro\Model\Collection;

use AlejandroHerr\BaseModel\Collection\AbstractCollection;

class SocioCollection extends AbstractCollection
{
    public function __construct($objects = array())
    {
        parent::__construct('Esnuab\Libro\Model\Entity\Socio', $objects);
    }
}
