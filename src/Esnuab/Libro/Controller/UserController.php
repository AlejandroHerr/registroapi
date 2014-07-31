<?php
namespace Esnuab\Libro\Controller;

use AlejandroHerr\ApiApplication\ApiController;
use Silex\Application;
use Esnuab\Libro\Model\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Esnuab\Libro\Form\UserForm;
use Esnuab\Libro\Services\CronTaskScheduler\CronTaskScheduler;

class UserController extends ApiController
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('', array($this,"getUsers"))
            ->before(array($this,"getQueryHeaders"));
        $controllers->post('', array($this,"postUser"))
            ->before($app['filter.only_superadmin'])
            ->before(array($this,"getFormHeaders"));
        $controllers->delete('/{id}', array($this,"deleteUser"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin']);
        $controllers->get('/{id}', array($this,"getUser"))
            ->assert('id', '\d+');
        $controllers->match('/{id}', array($this,"patchUser"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before(array($this,"getFormHeaders"))
            ->method('PATCH');
        $controllers->put('/{id}', array($this,"putUser"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before(array($this,"getFormHeaders"));
        $controllers->before($app['filter.only_admin']);

        return $controllers;
    }

    public function getUsers(Application $app)
    {
        $totalResults = $this->entityManager->getCount();
        $users = $this->entityManager->getCollection($this->queryParams);
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

    public function postUser(Application $app)
    {
        $user = new User();

        $this->form = $app['form.factory']->create(new UserForm(), $user);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->postResource($user);
        $this->taskScheduler->addUserTask(CronTaskScheduler::ACTION_CREATED,$user->getId());

        return $app->json('', 201);

    }

    public function deleteUser(Application $app, $id)
    {
        $this->entityManager->deleteResource($id);

        return $app->json(null, 204);
    }

    public function getUser(Application $app, $id)
    {
        $user = $this->entityManager->getResourceById($id);

        return $app->json($user->toArray(), 200);
    }

    public function patchUser(Application $app,$id)
    {
        return $this->updateUser($app, $id, false);
    }

    public function putUser(Application $app,$id)
    {
        return $this->updateUser($app, $id, true);
    }

    protected function updateUser(Application $app, $id, $clearMissing)
    {
        $user = $this->entityManager->getResourceById($id);

        $this->form = $app['form.factory']->create(new UserForm(), $user);
        $this->form->submit($this->data, $clearMissing);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->updateResource($user);
        $this->taskScheduler->addUserTask(CronTaskScheduler::ACTION_UPDATED,$user->getId());

        return $app->json('', 201);
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
