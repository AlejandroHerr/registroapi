<?php

namespace Esnuab\Services\TransactionLogger;
use Esnuab\Services\TransactionLogger\TransactionLog;

use Symfony\Component\HttpFoundation\Request;

class TransactionLogger{
	
	protected $transactionLog;
	
	protected $request;
	protected $data;
	protected $user;
	protected $target;

	public function __construct(){
		$this->transactionLog = new transactionLog();
	}
	public function setRequest(Request $request){
		$this->request=$request;
	}
	public function setData($data){
		$this->data=$data;
	}
	public function setUser($user){
		$this->user=$user;
	}
	public function setTargetByResponse(){
		$data = json_decode($this->data,true);
		$this->target = $data['id'];
	}
	public function setTargetByUri(){
		$this->target = substr(strrchr($this->request->getPathInfo(),"/"),1);
	}
	public function buildLog(){
		$this->transactionLog->setUser($this->user);
		$this->transactionLog->setIp($this->request->getClientIp());
		$this->transactionLog->setMethod($this->request->getMethod());
		$this->transactionLog->setTarget($this->target);
		$this->transactionLog->setData($this->data);
		$this->transactionLog->setTimestamp();
	}
	/*
	** Should be use for debug only
	*/
	public function printTransaction(){
		print_r($this->transactionLog->toArray());
	}

	public function recordLog($db){
		$db->insert('transactionLog',$this->transactionLog->toArray());
	}
}