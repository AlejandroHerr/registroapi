<?php
namespace Esnuab\Libro\Controller;

use AlejandroHerr\ApiApplication\ApiController;
use Silex\Application;
use Esnuab\Libro\Model\Entity\Socio;
use Esnuab\Libro\Form\SocioForm;
use Esnuab\Libro\Services\CronTaskScheduler\CronTaskScheduler;
use Symfony\Component\HttpFoundation\Request;

class SocioController extends ApiController
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('', array($this,"getSocios"))
            ->before($app['filter.only_admin'])
            ->before(array($this,"getQueryHeaders"));
        $controllers->post('', array($this,"postSocio"))
            ->before($app['filter.only_user'])
            ->before(array($this,"getFormHeaders"));
        $controllers->delete('/{id}', array($this,"deleteSocio"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin']);
        $controllers->get('/{id}', array($this,"getSocio"))
            ->assert('id', '\d+')
            ->before($app['filter.only_admin']);
        $controllers->match('/{id}', array($this,"patchSocio"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before(array($this,"getFormHeaders"))
            ->method('PATCH');
        $controllers->put('/{id}', array($this,"putSocio"))
            ->assert('id', '\d+')
            ->before($app['filter.only_superadmin'])
            ->before(array($this,"getFormHeaders"));

        return $controllers;
    }

    public function getSocios(Application $app)
    {
        $totalResults = $this->entityManager->getCount();
        $socios = $this->entityManager->getCollection($this->queryParams);
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

        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->postResource($socio);
        $this->taskScheduler->addSocioTask(CronTaskScheduler::ACTION_CREATED,$socio->getId());

        return $app->json('', 201);
    }

    public function deleteSocio(Application $app, $id)
    {
        $this->entityManager->deleteResource($id);

        return $app->json(null, 204);
    }

    public function getSocio(Application $app, $id)
    {
        $socio = $this->entityManager->getResourceById($id);

        return $app->json($socio->toArray(), 200);
    }

    public function patchSocio(Application $app, $id)
    {
        return $this->updateSocio($app, $id, $false);
    }

    public function putSocio(Application $app, $id)
    {
        return $this->updateSocio($app, $id, $true);
    }

    protected function updateSocio(Application $app, $id, $clearMissing)
    {
        $socio = $this->entityManager->getResourceById($id);

        $this->form = $app['form.factory']->create(new SocioForm(), $socio);
        $this->form->submit($this->data, $clearMissing);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->updateResource($socio);
        $this->taskScheduler->addSocioTask(CronTaskScheduler::ACTION_UPDATED,$socio->getId());

        return $app->json('', 204);
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
