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
        $socios = $this->socioManager->getCollection($app, $this->socioManager->beforeGetCollection($app, $this->queryParams));
        $response = array(
            'pagination' => array(
                'total' => $totalResults,
                'max' => $this->queryParams['max'],
                'page' => $this->queryParams['page']
            ),
            'socios' => $socios->toArray()
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

        $this->socioManager->beforePostResource($app, $socio);
        $this->socioManager->postResource($app, $socio);

        $this->transactionLogger->addNotice('Socio creado',array('datos'=>$socio->toArray()));

        return $app->json('', 201);
    }

    public function getSocio(Application $app, $id)
    {
        $socio = $this->socioManager->getResourceById($app, $id);

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

        $this->socioManager->beforePutResource($app, $socio);
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
        if (!array_key_exists('max', $this->queryParams)) {
            $this->queryParams['max'] = 25;
        }
        if (!is_numeric($this->queryParams['max'])) {
            $this->queryParams['max'] = 25;
        }
        if (!array_key_exists('page', $this->queryParams)) {
            $this->queryParams['page'] = 1;
        }
        if (!is_numeric($this->queryParams['page'])) {
            $this->queryParams['page'] = 1;
        }
        if (array_key_exists('dir', $this->queryParams)) {
            $this->queryParams['dir'] = $this->queryParams['dir'] == 'ASC' ? 'ASC' : 'DESC';
        } else {
            $this->queryParams['dir'] = 'DESC';
        }
        if (!array_key_exists('by', $this->queryParams)) {
            $this->queryParams['by'] = 'id';
        }
    }
}
