<?php
namespace Esnuab\Services;

use Silex\Provider\SwiftmailerServiceProvider;
class confirmUser{
	protected $mailer;
	protected $db;

	public function __construct($mailer,$db){
		$this->mailer=$mailer;
		$this->db=$db;
	}
	public function hola(){
		

		$message = \Swift_Message::newInstance()
			->setSubject('[YourSite] Feedback')
			->setFrom(array('info@esnuab.org'))
			->setTo(array('alejandrohnc88@gmail.com'))
			->setBody("hola");

		$this->mailer->send($message);
	}
}