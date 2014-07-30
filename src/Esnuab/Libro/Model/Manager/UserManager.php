<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\DuplicatedValueException;
use AlejandroHerr\ApiApplication\Model\Manager\AbstractDbalManager;
use Esnuab\Libro\Model\Entity\User;

class UserManager extends AbstractDbalManager
{
    protected $entity = 'Esnuab\Libro\Model\Entity\User';
    protected $collection = 'Esnuab\Libro\Model\Entity\UserCollection';
    protected $table = 'users';

    public function getCollection($queryParameters)
    {
        $queryParameters = array_map(array($this,'escape'), $queryParameters);
        $offset= ($queryParameters['page']-1)*$queryParameters['max'];
        $query = 'ORDER BY '.$queryParameters['by'].' '.$queryParameters['dir'].
            ' LIMIT '.$offset.','.$queryParameters['max'];

        $collection = parent::getCollection($query);

        return $collection->invoke('setPassword', array(''));
    }

    public function getResourceById($id)
    {
        $resource = parent::getResourceById($id);

        return $resource->setPassword('');
    }   

    public function postResource($resource)
    {
        if ($this->existsResource($resource->getUsername(),'username')) {
            throw new DuplicatedValueException('username');
        }
        if ($this->existsResource($resource->getEmail(),'email')) {
            throw new DuplicatedValueException('email');
        }
        $resource->setProtected(0)->setActive(1);

        return parent::postResource($resource);
    }

    public function putResource($resource)
    {
        if ($this->existsResource($resource->getUsername(),'username', $resource->getId())) {
            throw new DuplicatedValueException('username');
        }
        if ($this->existsResource($resource->getEmail(),'email', $resource->getId())) {
            throw new DuplicatedValueException('email');
        }

        return parent::putResource($resource);
    }
}
