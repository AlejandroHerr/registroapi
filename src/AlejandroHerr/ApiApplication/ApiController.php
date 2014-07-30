<?php
namespace AlejandroHerr\ApiApplication;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

abstract class ApiController implements ControllerProviderInterface
{
    protected $entityManager;
    protected $logger;
    protected $queryParams;
    protected $taskScheduler;

    public function __construct($entityManager, $logger = null, $taskScheduler = null)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->taskScheduler = $taskScheduler;
    }

    public function getFormHeaders(Request $request)
    {
        $this->data = json_decode($request->getContent(), true);
    }

    public function getQueryHeaders(Request $request)
    {
        $this->queryParams = $request->query->all();
    }

    protected function getFormErrorsAsArray(Form $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $key => $child) {
            if ($err = $this->getFormErrorsAsArray($child)) {
                $errors[$key] = $err;
            }
        }

        return $errors;
    }
}
