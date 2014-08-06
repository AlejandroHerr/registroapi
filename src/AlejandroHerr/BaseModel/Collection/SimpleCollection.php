<?php

namespace AlejandroHerr\BaseModel\Collection;

use Functional as F;

abstract class SimpleCollection
{
    protected $objects;

    public function __construct($objects = array())
    {
        $this->objects = F\map($objects,function ($object) {return $this->initMapper($object);});
    }

    public function constructFromObject($objects = array())
    {
        $this->objects = $objects;
    }

    public function dropFirst($n)
    {
        $this->objects = F\drop_first(
            $this->objects,
            function ($user, $index, $collection) use ($n) {
                return $index < $n;
            }
        );
    }

    public function dropLast($n)
    {
        $this->objects = F\drop_last(
            $this->objects,
            function ($user, $index, $collection) use ($n) {
                return $index < $n;
            }
        );
    }

    public function each(callable $callback)
    {
        F\each($this->objects, $callback);

        return $this;
    }

    public function getLength()
    {
        return count($this->objects);
    }

    public function getObject($n)
    {
        return $this->object[$n];
    }

    public function invoke($methodName, $methodArguments = array())
    {
        F\invoke($this->objects, $methodName, $methodArguments);

        return $this;
    }

    public function map(callable $callback)
    {
        return F\map($this->objects, $callback);
    }

    public function select(callable $callback, $asArray = false)
    {
        $selection = F\select($this->objects, $callback);
        if ($asArray) {
            return F\map($selection,function ($object) {return $this->arrayer($object);});
        }

        return $selection;
    }

    public function rebuildIndex()
    {
        $objects = $this->objects;
        $this->objects = null;

        foreach ($objects as $object) {
            $this->objects[] = $object;
        }

        return $this;
    }

    public function reject(callable $callback, $asArray = false)
    {
        $selection = F\reject($this->objects, $callback);
        if ($asArray) {
            return F\map($selection,function ($object) {return $this->arrayer($object);});
        }

        return $selection;
    }

    public function toArray()
    {
        return F\map($this->objects,function ($object) {return $this->arrayer($object);});
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
