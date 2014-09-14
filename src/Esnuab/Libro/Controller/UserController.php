<?php
namespace Esnuab\Libro\Controller;

use AlejandroHerr\JsonApi\Controller\CrudController;
use Symfony\Component\HttpFoundation\Request;

class UserController extends CrudController
{
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
