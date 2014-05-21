<?php
namespace Esnuab\Libro\Controller;

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
        $socios = $this->socioManager->getSocios($app, $this->queryParams);
        $response = array(
            'pagination' => array(
                'totalResults' => $totalResults,
                'maxResults' => $this->queryParams['maxResults'],
                'currentPage' => $this->queryParams['currentPage']
            ),
            'socios' => $socios
        );

        return $app->json($response, 200,$app['cors.headers']);
    }
    public function postSocio(Application $app)
    {
        return $this->processForm($app);
    }
    public function getSocio(Application $app, $id)
    {
        if (!$this->socioManager->existsSocio($app, $id)) {
            return $app->json(array('message' => 'El socio con id ' . $id . ' no existe.'), 404,$app['cors.headers']);
        }
        $socio = $this->socioManager->getSocio($app, $id);

        return $app->json($socio->toArray(), 200,$app['cors.headers']);
    }
    public function putSocio(Application $app, $id, Request $request)
    {
        if (!$this->socioManager->existsSocio($app, $id)) {
            return $app->json(array('message' => 'El socio con id ' . $id . ' no existe.'), 404,$app['cors.headers']);
        }

        return $this->processForm($app, $id);
    }
    public function deleteSocio(Application $app, $id)
    {
        if (!$this->socioManager->existsSocio($app, $id)) {
            return $app->json(array('message' => 'El socio con id ' . $id . ' no exise.'), 404,$app['cors.headers']);
        }
        $this->socioManager->deleteSocio($app, $id);

        return $app->json(null, 204,$app['cors.headers']);
    }
    public function processForm(Application $app, $id = null)
    {
        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $socio = new Socio();
        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, true);
        if ($this->form->isValid()) {
            if ($app['request']->getMethod() == 'POST') {
                if ($this->socioManager->existsSocio($app, $socio->getEsncard(), 'esncard')) {
                    return $app->json(array(
                        'errores' => array(
                            "esncard" => "La ESN Card ya existe"
                        )
                    ), 400, $app['cors.headers']);
                }
                if ($this->socioManager->existsSocio($app, $socio->getEmail(), 'email')) {
                    return $app->json(array(
                        'errores' => array(
                            "esncard" => "El e-mail ya existe"
                        )
                    ), 400, $app['cors.headers']);
                }
                $socio = $this->socioManager->createSocio($socio, $app);
                if (null !== $this->transactionLogger) {
                    $this->transactionLogger->addNotice('Socio creado',array('datos'=>$socio->toArray()));
                }
            }
            if ($app['request']->getMethod() == 'PUT') {
                if ($this->socioManager->existsSocio($app, $socio->getEsncard(), 'esncard', false, $id)) {
                    return $app->json(array(
                        'errores' => array(
                            "esncard" => "La ESN Card ya existe"
                        )
                    ), 400, $app['cors.headers']);
                }
                if ($this->socioManager->existsSocio($app, $socio->getEmail(), 'email', false, $id)) {
                    return $app->json(array(
                        'errores' => array(
                            "esncard" => "El e-mail ya existe"
                        )
                    ), 400, $app['cors.headers']);
                }
                $socio = $this->socioManager->updateSocio($socio, $app, $id);
                if (null !== $this->transactionLogger) {
                    $this->transactionLogger->addNotice('Socio actualizado',array('datos'=>$socio->toArray()));
                }
            }

            return $app->json($socio->toArray(), 201,$app['cors.headers']);
        }

        return $app->json(array('errores' => $this->getArray($this->form)), 400,$app['cors.headers']);
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
