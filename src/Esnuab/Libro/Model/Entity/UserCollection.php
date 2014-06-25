<?php

namespace Esnuab\Libro\Model\Entity;

use AlejandroHerr\ApiApplication\Model\Collection\SimpleCollection;

class UserCollection extends SimpleCollection
{
    protected $class = "Esnuab\Libro\Model\Entity\User";
    protected function getClass()
    {
        return "Esnuab\Libro\Model\Entity\User";
    }
}
