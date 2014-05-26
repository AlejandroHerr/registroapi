<?php
namespace Esnuab\Libro\Controller;

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
        $controllers->put('/users/{id}', array($this,"editUser"))
        ->assert('id', '\d+')
        ->before($app['filter.only_superadmin'])
        ->before(array($this,"getFormHeaders"));
        $controllers->before($app['filter.only_admin']);

        return $controllers;
    }

    public function getUsers(Application $app)
    {
        $totalResults = $this->userManager->getCount($this->queryParams);
        $users = $this->userManager->getUsers($this->queryParams);
        $response = array(
            'pagination' => array(
                'totalResults' => $totalResults,
                'maxResults' => $this->queryParams['maxResults'],
                'currentPage' => $this->queryParams['page'],
                'active' => $this->queryParams['active']
            ),
            'users' => $users
        );

        return $app->json($response, 200,$app['cors.headers']);
    }
    public function getUser(Application $app,$id)
    {
        if (!$this->userManager->existsUser($app, $id)) {
            return $app->json(array('message' => 'El user con id ' . $id . ' no existe.'), 404,$app['cors.headers']);
        }
        $user = $this->userManager->getUser($app,$id);

        return $app->json($user->toArray(), 200,$app['cors.headers']);
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
                ), 400, $app['cors.headers']);
            }
            if ($this->userManager->existsUser($app, $user->getUsername(), 'username')) {
                return $app->json(array(
                    'errores' => array(
                        "username" => "El username ya existe"
                    )
                ), 400, $app['cors.headers']);
            }

            $user = $this->userManager->createUser($user);

            if (null !== $this->transactionLogger) {
                $this->transactionLogger->addNotice('User creado',array('datos'=>$user->toArray()));
            }

            return $app->json(array('user' => $user->toArray()), 200,$app['cors.headers']);
        }

        return $app->json(array('errores' => $this->getArray($this->form)), 400,$app['cors.headers']);
    }
    public function editUser(Application $app,$id)
    {
        if (!$this->userManager->existsUser($app, $id)) {
            return $app->json(array('message' => 'El user con id ' . $id . ' no existe.'), 404,$app['cors.headers']);
        }
        $app->register(new FormServiceProvider());
        $app->register(new ValidatorServiceProvider());
        $user = new User();
        $this->form = $app['form.factory']->create(new UserForm(), $user);
        $this->form->submit($this->data, true);
        if ($this->form->isValid()) {
            if ($this->userManager->existsUser($app, $user->getEmail(), 'email')) {
                return $app->json(array(
                    'errores' => array(
                        "email" => "El e-mail ya existe"
                    )
                ), 400, $app['cors.headers']);
            }
            if ($this->userManager->existsUser($app, $user->getUsername(), 'username')) {
                return $app->json(array(
                    'errores' => array(
                        "username" => "El username ya existe"
                    )
                ), 400, $app['cors.headers']);
            }
            $user = $this->userManager->updateUser($user,$id);
            if (null !== $this->transactionLogger) {
                $this->transactionLogger->addNotice('User Actualizado',array('datos'=>$user->toArray()));
            }

            return $app->json(array('user' => $user->toArray()), 200,$app['cors.headers']);
        }

        return $app->json(array('errores' => $this->getArray($this->form)), 400,$app['cors.headers']);
    }
    public function getQueryHeaders(Request $request)
    {
        $this->queryParams = $request->query->all();
        $this->queryParams['page'] = !isset($this->queryParams['page']) ? 1 : $this->queryParams['page'];
        $this->queryParams['maxResults'] = !isset($this->queryParams['maxResults']) ? 10 : $this->queryParams['maxResults'];
        $this->queryParams['active'] = !isset($this->queryParams['active']) ? 0 : $this->queryParams['active'];
    }
}
