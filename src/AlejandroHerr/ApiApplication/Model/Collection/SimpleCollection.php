<?php

namespace AlejandroHerr\ApiApplication\Model\Collection;

use Functional as F;

abstract class SimpleCollection
{
    protected $objects;

    public function __construct($objects = array())
    {
        $this->objects = F\map($objects,function ($object) {return $this->initMapper($object);});
    }

    public function toArray()
    {
        return F\map($this->objects,function ($object) {return $this->arrayer($object);});
    }

    public function invoke($methodName, $methodArguments = array())
    {
        F\invoke($this->objects, $methodName, $methodArguments);
        return $this;
    }

    private function arrayer($object)
    {
        return $object->toArray();
    }

    private function initMapper($object)
    {
        return new $this->class($object);
    }
}
