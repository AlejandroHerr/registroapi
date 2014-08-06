<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\BaseModel\Exception\DuplicatedValueException;
use AlejandroHerr\BaseModel\Manager\AbstractDbalManager;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

class UserManager extends AbstractDbalManager
{
    public function __construct(Connection $conn, LoggerInterface $logger = null)
    {
        $model = array(
            'entity' => 'Esnuab\Libro\Model\Entity\User',
            'collection' => 'Esnuab\Libro\Model\Collection\UserCollection',
            'table' => 'users'
        );
        parent::__construct($conn, $model, $logger);
    }

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

    public function updateResource($resource)
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
