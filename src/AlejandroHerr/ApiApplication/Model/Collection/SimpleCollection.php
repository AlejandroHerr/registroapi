<?php

namespace AlejandroHerr\ApiApplication\Model\Collection;

abstract class SimpleCollection
{
    protected $objects;

    public function __construct($objects = array())
    {
        $this->objects = array_map(array($this,'initMapper'), $objects);
    }

    public function toArray()
    {
        return array_map(array($this,'arrayer'), $this->objects);
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
