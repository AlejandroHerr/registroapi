<?php
namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Esnuab\Libro\Form\SocioForm;
use Esnuab\Libro\Model\Entity\Socio;
use Esnuab\Libro\Model\SocioManager;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController implements ControllerProviderInterface {
	protected $socioManager;
	protected $app;
	protected $form;
	
	function __construct($socioManager) {
		$this->socioManager = $socioManager;
	}
	
	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		
		$controllers->get('/socios/', array($this,"getSocios"))
		->bind('getSociosAction');

		$controllers->post('/socios/',array($this,"postSocio"))
		->bind('postSocioAction');

		$controllers->put('/socios/{id}/',array($this,"putSocio"))
		->assert('id', '\d+')
		->bind('putSocioAction');

		$controllers->get('/socios/{id}/', array($this,"getSocio"))
		->assert('id', '\d+')
		->bind('getSocioAction');

		$controllers->before(function(Request $request) {
			if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        		$data = json_decode($request->getContent(), true);
        		$request->request->replace(is_array($data) ? $data : array());
    		}
		});
		return $controllers;
	}
	
	function getSocios(Application $app) {
		$socios = $this->socioManager->getSocios($app);
		return $app->json($socios,200);
	}

	function getSocio(Application $app,$id) {
		$socio = $this->socioManager->getSocio($app,$id);
		if($socio){
			return $app->json($socio->toArray(),200);
		}
		$error = array( 
			'error' => array(
				'code' => '400',
				'message' => 'Socio no existe'
			)
		);
		return $app->json($error,400);
	}

	function postSocio(Application $app, Request $request){	
		return $this->processForm($app,$request);
	}
	function putSocio(Application $app,$id,Request $request){
		if($this->socioManager->socioExists($app,$id)){
			return $this->processForm($app,$request,$id);
		}
		$error = array( 
			'error' => array(
				'code' => '400',
				'message' => 'Socio no existe'
			)
		);
		return $app->json($error,400);
	}

	function processForm(Application $app, Request $request,$id=null){
		$app->register(new FormServiceProvider());
		$app->register(new ValidatorServiceProvider());
		$socio = new Socio();
	   	$this->form = $app['form.factory']->create(new SocioForm(),$socio);
		$this->form->submit($request);
		if ($this->form->isValid()) {
			if($request->getMethod() == 'POST'){
				$socio=$this->socioManager->createSocio($socio,$app);
			}
			if($request->getMethod() == 'PUT'){
				$socio=$this->socioManager->updateSocio($socio,$app,$id);
			}
			return $app->json($socio->toArray(),201);
		}
		return $app->json($this->form->getErrors(),400);
	}
}