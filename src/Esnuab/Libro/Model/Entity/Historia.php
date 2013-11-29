<?php
namespace Esnuab\Libro\Model\Entity;
class Historia extends Base {

	protected $id;
	protected $user;
	protected $action;
	protected $target;
	protected $timestamp;

	public function setUser($user){
		$this->user=$user;
	}
	public function setAction($action){
		$this->action=$action;
	}
	public function setTarget($target){
		$this->target=$target;
	}
}
