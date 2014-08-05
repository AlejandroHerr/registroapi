<?php
namespace AlejandroHerr\JsonApi;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;

abstract class JsonController
{
    protected $data;
    protected $queryParams;

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
