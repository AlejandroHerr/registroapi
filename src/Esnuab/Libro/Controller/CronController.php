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
	function confirmSocios(Application $app){
		$subjects = $this->confirmationManager->getUnconfirmed($app);
		$mergeVars = $this->confirmationManager->getMergeVars($subjects);
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
	    $result = $this->mandrill->messages->send($message, $async, $ip_pool, $send_at);
	    $notConfirmed=$this->getNotConfirmed($result);
	    $this->confirmationManager->recordConfirmations($app,$subjects,$notConfirmed);
	    return $app->redirect('/cron/limpiar');
	}
	function getNotConfirmed($result)
	{
		$notConfirmed = array();
		foreach ($result as $value) {
			if($value['status']=='invalid'){
				$notConfirmed[]=array(
					'email' => $value['email'],
					'error' => 'invalid'
				);
			}
			if($value['status']=='rejected'){
				$notConfirmed[]=array(
					'email' => $value['email'],
					'error' => $value['reject_reason']
				);
			}
		}
		return $notConfirmed;
	}
	function deleteConfirmed(Application $app)
	{
		$this->confirmationManager->deleteConfirmed($app);
		return new Response('',201);
	}
}