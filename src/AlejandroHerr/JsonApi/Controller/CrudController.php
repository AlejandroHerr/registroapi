<?php
namespace AlejandroHerr\JsonApi\Controller;

use AlejandroHerr\JsonApi\Model\Manager\AbstractDbalManager;
use Psr\Log\LoggerInterface;
use Silex\Application
use Symfony\Component\Form\Form;

class CrudController extends JsonController
{
    protected $entity;
    protected $entityManager;
    protected $form;
    protected $logger;
    protected $taskScheduler;

    public function __construct(AbstractDbalManager $entityManager, $entity, $form, LoggerInterface $logger = null, $taskScheduler = null)
    {
        $this->entity = $entity;
        $this->entityManager = $entityManager;
        $this->form = $form;
        $this->logger = $logger;
        $this->taskScheduler = $taskScheduler;
    }

    public function getCollection(Application $app)
    {
        $totalResults = $this->entityManager->getCount();
        $collection = $this->entityManager->getCollection($this->queryParams);
        $response = array(
            'pagination' => array(
                'total' => $totalResults,
                'max' => $this->queryParams['max'],
                'page' => $this->queryParams['page']
            ),
            'collection' => $collection->toArray()
        );

        return $app->json($response, 200);
    }

    public function postResource(Application $app)
    {
        $resource = new $this->entity();

        $this->form = $app['form.factory']->create(new $this->form(), $resource);
        $this->form->submit($this->data, true);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->postResource($resource);
        if (null !== $this->taskScheduler) {
            $type = explode('\\', $this->entity);
            $this->taskScheduler->addTask($type[count($type)-1], 'created', $resource->getId());
        }

        return $app->json('', 201);
    }

    public function deleteResource(Application $app, $id)
    {
        $this->entityManager->deleteResource($id);

        return $app->json(null, 204);
    }

    public function getResource(Application $app, $id)
    {
        $resource = $this->entityManager->getResourceById($id);

        return $app->json($resource->toArray(), 200);
    }

    public function patchResource(Application $app, $id)
    {
        return $this->updateResource($app, $id, $false);
    }

    public function putResource(Application $app, $id)
    {
        return $this->updateResource($app, $id, $true);
    }

    protected function updateResource(Application $app, $id, $clearMissing)
    {
        $resource = $this->entityManager->getResourceById($id);

        $this->form = $app['form.factory']->create(new $this->form(), $resource);
        $this->form->submit($this->data, $clearMissing);

        if (!$this->form->isValid()) {
            return $app->json(array('errores' => $this->getFormErrorsAsArray($this->form)), 400);
        }

        $this->entityManager->updateResource($resource);
        if (null !== $this->taskScheduler) {
            $type = explode('\\', $this->entity);
            $this->taskScheduler->addTask($type[count($type)-1], 'updated', $resource->getId());
        }

        return $app->json('', 204);
    }

}
