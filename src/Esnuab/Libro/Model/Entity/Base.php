<?php

namespace Esnuab\Libro\Model\Entity;

abstract class Base{
  function __construct(array $data=array()){
	  foreach ($data as $key => $value) {
		$this->__set($key,$value);
	  }
	}

	function __get($name) {
	  $method = "get".ucwords($name);
	  if(method_exists($this,$method)):
		return $this->$method();
	  elseif (property_exists($this, $name)):
		return $this->$name;
	  endif;
	}

	function __set($name, $value) {
	  $method = "set".ucwords($name);
	  if(method_exists($this, $method)):
		return $this->$method($value);
	  elseif (property_exists($this, $name)):
		$this->$name = $value;
	  endif;
	}
 function __toString(){
	  ob_start();
	  var_dump($this);
	  return ob_get_clean();
	}

	/**
	 * get properties as an associative array 
	 * and trim null value
	 * @see http://briancray.com/posts/remove-null-values-php-arrays
	 */
	function toArray(){
	  $array = @array_filter( get_object_vars($this) ,function($value){
		return $value!=null;
	  });
	  return $array;
	}

	function serialize(){
	  return json_encode($this->toArray());
	}

	function deszerialize($json){
	  $datas = json_decode($json);
	  $this->__construct($json);
	}
}