<?php

namespace AlejandroHerr\ApiApplication\Model\Entity;

abstract class AbstractEntity
{
    public function __construct(array $data=array())
    {
        foreach ($data as $key => $value) {
            $this->__set($key,$value);
        }
    }

    public function __get($name)
    {
        $method = "get".ucwords($name);
        if(method_exists($this,$method)):
            return $this->$method();
        elseif (property_exists($this, $name)):
            return $this->$name;
        endif;
    }

    public function __set($name, $value)
    {
        $method = "set".ucwords($name);
        if(method_exists($this, $method)):
            return $this->$method($value);
        elseif (property_exists($this, $name)):
            $this->$name = $value;
        endif;
    }
     function __toString()
     {
        ob_start();
        var_dump($this);

        return ob_get_clean();
    }
    public function toArray()
    {
        $array = @array_filter( get_object_vars($this) ,function ($value) {
            return $value!=null;
        });

        return $array;
    }

    public function serialize()
    {
      return json_encode($this->toArray());
    }

    public function deszerialize($json)
    {
        $datas = json_decode($json);
        $this->__construct($json);
    }
}
