<?php

namespace Esnuab\Libro\Model\Entity;

use AlejandroHerr\ApiApplication\Model\Collection\SimpleCollection;

class SocioCollection extends SimpleCollection
{
    protected $class = "Esnuab\Libro\Model\Entity\Socio";
    protected function getClass()
    {
        return "Esnuab\Libro\Model\Entity\Socio";
    }
}
