<?php

namespace Esnuab\Logger;

//use Esnuab\Libro\Model\Entity\Socio;
class Logger{
	protected $request;
	protected $response;

	protected $db;
	protected $table;

	public function __construct($db,$table){
		$this->db=$db;
		$this->table=$table;

	}
	public function set($request,$response){
		$this->response = $response;
		$this->request = $request;
	}
	public function createLog(){
	/*	echo "<br>";
		echo $this->request->getMethod();
		echo "<br>";
		echo $this->request->getClientIp();
		echo "<br>";
		echo $this->request->getRequestUri();
		echo "<br>";
		echo "sdsdsds";
		echo $this->request->getUri();
		echo "<br>";
		echo "<br>";*/
	}
}