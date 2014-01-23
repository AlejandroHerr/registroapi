<?php
namespace Esnuab\Services\TransactionLogger;
class TransactionLog {
	protected $id;
	protected $user;
	protected $ip;
	protected $method;
	protected $target;
	protected $data;
	protected $timestamp;
	//BASIC FUNCTIONS
	public function __construct(array $data = array()) {
		foreach ($data as $key => $value) {
			$this->__set($key, $value);
		}
	}
	public function __get($name) {
		$method = "get" . ucwords($name);
		if (method_exists($this, $method)):
			return $this->$method();
		elseif (property_exists($this, $name)):
			return $this->$name;
		endif;
	}
	public function __set($name, $value) {
		$method = "set".ucwords($name);
		if(method_exists($this, $method)):
			return $this->$method($value);
		elseif (property_exists($this, $name)):
			$this->$name = $value;
		endif;
	}
	public function __toString() {
		ob_start();
		var_dump($this);
		return ob_get_clean();
	}
	public function toArray() {
		$array = @array_filter(get_object_vars($this), function($value) {
			return $value != null;
		});
		return $array;
	}
	public function serialize() {
		return json_encode($this->toArray());
	}
	public function deszerialize($json) {
		$datas = json_decode($json);
		$this->__construct($json);
	}
	//GETTERS AND SETTERS
	public function getId() {
		return $this->id;
	}
	public function setId($id) {
		$this->id = $id;
		return $this;
	}
	public function getUser() {
		return $this->user;
	}
	public function setUser($user) {
		$this->user = $user;
		return $this;
	}
	public function getIp() {
		return $this->ip;
	}
	public function setIp($ip) {
		$this->ip = $ip;
		return $this;
	}
	public function getMethod() {
		return $this->method;
	}
	public function setMethod($method) {
		$this->method = $method;
		return $this;
	}
	public function getTarget() {
		return $this->target;
	}
	public function setTarget($target) {
		$this->target = $target;
		return $this;
	}
	public function getData() {
		return $this->data;
	}
	public function setData($data) {
		$this->data = $data;
		return $this;
	}
	public function getTimestamp() {
		return $this->timestamp;
	}
	public function setTimestamp() {
		$this->timestamp = time();
		return $this;
	}
}