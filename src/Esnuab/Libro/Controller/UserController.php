<?php
namespace Esnuab\Libro\Controller;

use AlejandroHerr\ApiApplication\ApiController;
use Silex\Application;
use Esnuab\Libro\Model\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Esnuab\Libro\Form\NewUserForm;
use Esnuab\Libro\Form\UserForm;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

class AdminController extends ApiController
{
    protected $transactionLogger;
    protected $userManager;
    protected $queryParams;
    public function __construct($userManager,$transactionLogger=null)
    {
        $this->userManager=$userManager;
        $this->transactionLogger = $transactionLogger;
    }
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $controllers->get('/users', array($this,"getUsers"))
        ->before(array($this,"getQueryHeaders"));
        $controllers->post('/users', array($this,"postUser"))
        ->before($app['filter.only_superadmin'])
        ->before(array($this,"getFormHeaders"));
        $controllers->get('/users/{id}', array($this,"getUser"))
        ->assert('id', '\d+');
        $controllers->put('/users/{id}', array($this,"putUser"))
        ->assert('id', '\d+')
        ->before($app['filter.only_superadmin'])
        ->before(array($this,"getFormHeaders"));
        $controllers->get('/users/{action}/{id}', array($this,"blockUser"))
        ->assert('id', '\d+')
        ->before($app['filter.only_superadmin']);
        $controllers->before($app['filter.only_admin']);

        return $controllers;
    }

    public function getUsers(Application $app)
    {
        $totalResults = $this->userManager->getCount($app);
        $users = $this->userManager->getCollection($app, $this->userManager->beforeGetCollection($app, $this->queryParams));
        $users->invoke('setPassword',array(''));
        $response = array(
            'pagination' => array(
                'total' => $totalResults,
                'max' => $this->queryParams['max'],
                'page' => $this->queryParams['page']
            ),
            'users' => $users->toArray()
        );

        return $app->json($response, 200);
    }
    public function getUser(Application $app,$id)
    {
        $user = $this->userManager->getResourceById($app, $id);
        $user->setPassword('');

        return $app->json($user->toArray(), 200);
    }
    public function postUser(Application $app)
    {
        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $user = new User();
        $this->form = $app['form.factory']->create(new NewUserForm(), $user);
        $this->form->submit($this->data, true);
        if ($this->form->isValid()) {
            if ($this->userManager->existsUser($app, $user->getEmail(), 'email')) {
                return $app->json(array(
                    'errores' => array(
                        "email" => "El e-mail ya existe"
                    )
                ), 400);
            }
            if ($this->userManager->existsUser($app, $user->getUsername(), 'username')) {
                return $app->json(array(
                    'errores' => array(
                        "username" => "El username ya existe"
                    )
                ), 400);
            }

            $user = $this->userManager->createUser($user);

            if (null !== $this->transactionLogger) {
                $this->transactionLogger->addNotice('User creado',array('datos'=>$user->toArray()));
            }

            return $app->json(array('user' => $user->toArray()), 200);
        }

        return $app->json(array('errores' => $this->getArray($this->form)), 400);
    }
    public function putUser(Application $app,$id)
    {
        /*$user = $this->userManager->getResourceById($app, $id);

        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());

        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->socioManager->beforePutResource($app, $socio);
        $this->socioManager->putResource($app, $socio);

        $this->transactionLogger->addNotice('Socio actualizado',$socio->toArray());

        return $app->json('', 204);*/
    }
    public function blockUser(Application $app,$action,$id)
    {
        if (!$this->userManager->existsUser($app, $id)) {
            return $app->json(array('message' => 'El user con id ' . $id . ' no existe.'), 404);
        }
        $user = new User();
        if ($action == 'block') {
            $user->setBlocked(1);
        } elseif ($action == 'unblock') {
            $user->setBlocked(0);
        } else {
            return $app->json(array('message' => 'Accion no permitida.'), 404);
        }
        $this->userManager->updateUser($user,$id);

        return $app->json(array(''),204);
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
        if (array_key_exists('by', $this->queryParams)) {
            $fields = array(
                'id',
                'nombre',
                'apellido',
                'email',
                'esncard',
                'passport',
                'pais',
                'id'
            );
            $this->queryParams['by'] = in_array($this->queryParams['by'], $fields) ? $this->queryParams['by'] : 'id';
        } else {
            $this->queryParams['by'] = 'id';
        }
    }
}
