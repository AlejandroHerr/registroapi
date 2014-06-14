<?php
namespace Esnuab\Libro\Controller;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class ApiController implements ControllerProviderInterface
{
    public function getFormHeaders(Request $request)
    {
        $this->data = json_decode($request->getContent(), true);
    }
    public function getQueryHeaders(Request $request)
    {
        $this->queryParams = $request->query->all();
    }
    public function getArray(\Symfony\Component\Form\Form $form)
    {
        return $this->getErrors($form);
    }

    public function getErrors($form)
    {
        $errors = array();
        if ($form instanceof \Symfony\Component\Form\Form) {
            foreach ($form->getErrors() as $error) {

                $errors[] = $error->getMessage();
            }

            foreach ($form->all() as $key => $child) {
                if ($err = $this->getErrors($child)) {
                    $errors[$key] = $err;
                }
            }
        }

        return $errors;
    }
}
