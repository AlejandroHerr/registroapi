<?php
namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;

class CronController implements ControllerProviderInterface
{
	protected $confirmationManager;
	protected $mandrill;
	function __construct($confirmationManager,$mandrill) {
		$this->mandrill=$mandrill;
		$this->confirmationManager = $confirmationManager;
	}
	public function connect(Application $app)
	{	
		$controllers = $app['controllers_factory'];
		$controllers->get('/confirmar',array($this,"confirmSocios"));
		$controllers->get('/limpiar',array($this,"deleteConfirmed"));
		return $controllers;
	}
	function confirmSocios(Application $app)
	{
		$this->confirmationManager->loadUnconfirmed($app);
		$results = $this->sendConfirmation($this->confirmationManager->getUnconfirmed(),$this->confirmationManager->getMergeVars());
		$this->confirmationManager->processResult($app,$results);
		return new Response('',200);
	}
	function sendConfirmation($subjects,$mergeVars)
	{
		$message = array(
	        'html' => '<b>hola *|NAME|*,</b><br>tu ESNCard, n&uacute;mero *|ESNCARD|*, es v&aacute;lida hasta el *|EXPIREDATE|* <br><br> Un saludo,<br>el equipo deErasmus Student Network Universitat Autonoma de Barcelona',
	        'text' => 'Example text content',
	        'subject' => 'Bienvenido a ESN UAB!',
	        'from_email' => 'no-reply@esnuab.org',
        	'from_name' => 'Erasmus Student Network UAB',
	        'to' => $subjects,
	        'merge_vars' => $mergeVars
	    );
	    $async = false;
	    $ip_pool = 'Main Pool';
	    $send_at = strtotime ('YYYY-MM-DD HH:MM:SS');
	    return $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
	}
}