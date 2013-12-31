<?php
namespace Esnuab\Libro\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\Form\Form;

use Esnuab\Libro\Form\SocioForm;
use Esnuab\Libro\Model\Entity\Socio;
use Esnuab\Libro\Model\Manager\SocioManager;

use Esnuab\Libro\Form\HistoriaForm;
use Esnuab\Libro\Model\Entity\Historia;
use Esnuab\Libro\Model\Manager\HistoriaManager;


use Silex\Provider\FormServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ApiController implements ControllerProviderInterface {
	protected $socioManager;
	protected $historiaManager;

	protected $data;

	protected $form;
	
	function __construct($socioManager,$historiaManager) {
		$this->historiaManager = $historiaManager;
		$this->socioManager = $socioManager;
	}
	
	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];
		
		$controllers->get('/socios', array($this,"getSocios"))
		->bind('getSociosAction')
		->before($app['filter.only_admin'])
		->before(array($this,"getQueryHeaders"));
		/*->before(function(Request $request) {
			$this->data = array(
				'maxResults' => 25,
				'currentPage' => 1,
				'orderBy' => 'created_at',
				'orderDir' => 'DESC'
			);
			if($request->headers->get('Query-Options')){
				$data=json_decode($request->headers->get('Query-Options'),true);
				foreach ($data as $key => $value) {
					$this->data[$key]=$value;
				}
			}
		});*/

		$controllers->post('/socios', array($this,"postSocio"))
		->bind('postSocioAction')
		->before($app['filter.only_user'])
		->before(array($this,"getFormHeaders"));

		$controllers->get('/socios/{id}', array($this,"getSocio"))
		->assert('id', '\d+')
		->bind('getSocioAction')
		->before($app['filter.only_admin']);

		$controllers->put('/socios/{id}',array($this,"putSocio"))
		->assert('id', '\d+')
		->bind('putSocioAction')
		->before($app['filter.only_superadmin'])
		->before(array($this,"getFormHeaders"));

		$controllers->delete('/socios/{id}', array($this,"deleteSocio"))
		->assert('id', '\d+')
		->bind('deleteSocioAction')
		->before($app['filter.only_admin']);

		/*$controllers->before(function(Request $request) {
			$this->data = json_decode($request->getContent(), true);
			if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
        		//print_r($request->getContent());
        		
        		//$request->request->replace(is_array($data) ? $data : array());
    		}
		});*/
		
		return $controllers;
	}
	//DEFINITIVO	
	function getSocios(Application $app) {
		$totalResults = $this->socioManager->getCount($app);
		$socios = $this->socioManager->getSocios($app,$this->data);
		$response = array(
			'pagination' => array(
				'totalResults' => $totalResults,
				'maxResults' => $this->data['maxResults'],
				'currentPage' => $this->data['currentPage']
			),
			'socios' => $socios
		);
		return $app->json($response,200);
	}

	function postSocio(Application $app){
		if(isset($this->data['esncard'])){
			if($this->socioManager->existsEsncard($app,$this->data['esncard'])){
				return $app->json(array('errores' => array("esncard" => "La ESN Card ya existe")),400);
			}
		}
		return $this->processForm($app);
	}
	//DEFINITIVO
	function getSocio(Application $app,$id) {
		if(!$this->socioManager->existsSocio($app,$id)){
			return $app->json(array('message'=>'El socio con id '.$id.' no exise.'),404);
		}
		$socio = $this->socioManager->getSocio($app,$id);
		return $app->json($socio->toArray(),200);		
	}

	function putSocio(Application $app,$id,Request $request){
		if($this->socioManager->existsSocio($app,$id)){
			return $app->json(array('message'=>'El socio con id '.$id.' no exise.'),404);
		}
		return $this->processForm($app,$request,$id);
	}
	//DEFINITIVO
	function deleteSocio(Application $app,$id){
		if(!$this->socioManager->existsSocio($app,$id)){
			return $app->json(array('message'=>'El socio con id '.$id.' no exise.'),404);
		}
		$this->socioManager->deleteSocio($app,$id);
		return $app->json(null,204);
	}

	function processForm(Application $app,$id=null){
		$app->register(new FormServiceProvider());
		$app->register(new ValidatorServiceProvider());
		$socio = new Socio();
	   	$this->form = $app['form.factory']->create(new SocioForm(),$socio);
		$this->form->submit($this->data,true);
		if ($this->form->isValid()) {
			if($app['request']->getMethod() == 'POST'){
				//comprobar si esn card existe
				$socio=$this->socioManager->createSocio($socio,$app);
			}
			if($app['request']->getMethod() == 'PUT'){
				//comprobar si esn card existe
				$socio=$this->socioManager->updateSocio($socio,$app,$id);
			}
			//$this->postHistoria($app,$request->getMethod(),$socio->getId());
			return $app->json($socio->toArray(),201);
		}
		/**************************************************/

		return $app->json(array('errores' => $this->form->getErrorsAsArray()),400);
	}

	function parseOptions(Application $app, Request $request){
		$this->data = array(
			'maxResults' => 25,
			'currentPage' => 1,
			'orderBy' => 'created_at',
			'orderDir' => 'DESC'
		);
		if($app['request']->headers->get('Query-Options')){
			$data=json_decode($app['request']->headers->get('Query-Options'),true);
			foreach ($data as $key => $value) {
				$this->data[$key]=$value;
			}
		}
	}

	function getFormHeaders(Request $request) {
		$this->data = json_decode($request->getContent(), true);
	}
	function getQueryHeaders(Request $request) {
		$this->data = array(
			'maxResults' => 25,
			'currentPage' => 1,
			'orderBy' => 'created_at',
			'orderDir' => 'DESC'
		);
		if($request->headers->get('Query-Options')){
			$data=json_decode($request->headers->get('Query-Options'),true);
			foreach ($data as $key => $value) {
				$this->data[$key]=$value;
			}
		}
	}
}