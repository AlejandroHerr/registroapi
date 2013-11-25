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
		return $app->json($socios);
	}

	function getSocio(Application $app,$id) {
		return $this->socioManager->getSocio($app,$id);
	}

	function postSocio(Application $app, Request $request){
		
		return $this->processForm($app,$request);
	}

	function processForm(Application $app, Request $request){
		$app->register(new FormServiceProvider());
		$app->register(new ValidatorServiceProvider());
		$socio = new Socio();
	   	$this->form = $app['form.factory']->create(new SocioForm(),$socio);
		$this->form->submit($request);
		if ($this->form->isValid()) {
			if($request->getMethod() == 'POST'){
				$socio=$this->socioManager->createSocio($socio,$app);
				return $socio->serialize();
			}
		}
		print_r($this->form->getErrors());
		return $app->json($this->form);

	}
}