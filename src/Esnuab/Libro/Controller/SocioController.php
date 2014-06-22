<?php
namespace Esnuab\Libro\Controller;

use AlejandroHerr\ApiApplication\ApiController;
use Silex\Application;
use Esnuab\Libro\Model\Entity\Socio;
use Esnuab\Libro\Model\Manager\SocioManager;
use Esnuab\Libro\Form\SocioForm;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Symfony\Component\HttpFoundation\Request;

class SocioController extends ApiController
{
    protected $socioManager;
    protected $transactionLogger;
    public function __construct($socioManager,$transactionLogger=null)
    {
        $this->transactionLogger = $transactionLogger;
        $this->socioManager = $socioManager;
    }
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/socios', array($this,"getSocios"))
        ->before($app['filter.only_admin'])
        ->before(array($this,"getQueryHeaders"));
        $controllers->post('/socios', array($this,"postSocio"))
        ->before($app['filter.only_user'])
        ->before(array($this,"getFormHeaders"));
        $controllers->get('/socios/{id}', array($this,"getSocio"))
        ->assert('id', '\d+')
        ->before($app['filter.only_admin']);
        $controllers->put('/socios/{id}', array($this,"putSocio"))
        ->assert('id', '\d+')
        ->before($app['filter.only_superadmin'])
        ->before(array($this,"getFormHeaders"));
        $controllers->delete('/socios/{id}', array($this,"deleteSocio"))
        ->assert('id', '\d+')
        ->before($app['filter.only_superadmin']);

        return $controllers;
    }

    public function getSocios(Application $app)
    {
        $totalResults = $this->socioManager->getCount($app);
        $socios = $this->socioManager->getResources($app, $this->queryParams);
        $response = array(
            'pagination' => array(
                'totalResults' => $totalResults,
                'maxResults' => $this->queryParams['maxResults'],
                'currentPage' => $this->queryParams['currentPage']
            ),
            'socios' => $socios
        );

        return $app->json($response, 200);
    }
    public function postSocio(Application $app)
    {
        $socio = new Socio();

        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());

        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->socioManager->postResource($app, $socio);

        $this->transactionLogger->addNotice('Socio creado',array('datos'=>$socio->toArray()));

        return $app->json('', 201);
    }
    public function getSocio(Application $app, $id)
    {
        $socio = $this->socioManager->getResource($app, $id);

        return $app->json($socio->toArray(), 200);
    }
    public function putSocio(Application $app, $id, Request $request)
    {
        $socio = $this->socioManager->getResource($app, $id);

        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());

        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->socioManager->putResource($app, $socio);

        $this->transactionLogger->addNotice('Socio actualizado',array('datos'=>$socio->toArray()));

        return $app->json('', 204);
    }
    public function deleteSocio(Application $app, $id)
    {
        $this->socioManager->deleteResource($app, $id);

        $this->transactionLogger->addNotice('Socio eliminado',array('datos'=>$id));

        return $app->json(null, 204);
    }

    public function getQueryHeaders(Request $request)
    {
        $this->queryParams = $request->query->all();
        if (!array_key_exists('maxResults', $this->queryParams)) {
            $this->queryParams['maxResults'] = 25;
        }
        if (!is_numeric($this->queryParams['maxResults'])) {
            $this->queryParams['maxResults'] = 25;
        }
        if (!array_key_exists('currentPage', $this->queryParams)) {
            $this->queryParams['currentPage'] = 1;
        }
        if (!is_numeric($this->queryParams['currentPage'])) {
            $this->queryParams['currentPage'] = 1;
        }
        if (array_key_exists('orderDir', $this->queryParams)) {
            $this->queryParams['orderDir'] = $this->queryParams['orderDir'] == 'ASC' ? 'ASC' : 'DESC';
        } else {
            $this->queryParams['orderDir'] = 'DESC';
        }
        if (array_key_exists('orderBy', $this->queryParams)) {
            $fields = array(
                'id',
                'nombre',
                'apellido',
                'email',
                'esncard',
                'passport',
                'pais',
                'created_at'
            );
            $this->queryParams['orderBy'] = in_array($this->queryParams['orderBy'], $fields) ? $this->queryParams['orderBy'] : 'created_at';
        } else {
            $this->queryParams['orderBy'] = 'created_at';
        }
    }
}
