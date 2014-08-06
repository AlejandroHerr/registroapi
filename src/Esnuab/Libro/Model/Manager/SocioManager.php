<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\BaseModel\Exception\DuplicatedValueException;
use AlejandroHerr\BaseModel\Manager\AbstractDbalManager;
use Doctrine\DBAL\Connection;
use Esnuab\Libro\Model\Entity\Socio;
use Psr\Log\LoggerInterface;

class SocioManager extends AbstractDbalManager
{
    public function __construct(Connection $conn, LoggerInterface $logger = null)
    {
        $model = array(
            'entity' => 'Esnuab\Libro\Model\Entity\Socio',
            'collection' => 'Esnuab\Libro\Model\Collection\SocioCollection',
            'table' => 'socio'
        );
        parent::__construct($conn, $model, $logger);
    }

    public function getCollection($queryParameters)
    {
        $queryParameters = array_map(array($this,'escape'), $queryParameters);
        $offset=($queryParameters['page']-1)*$queryParameters['max'];
        $query =  'ORDER BY '.$queryParameters['by'].' '.$queryParameters['dir'].
            ' LIMIT '.$offset.','.$queryParameters['max'];

        return parent::getCollection($query);
    }

    public function postResource($resource)
    {
        if ($this->existsResource($resource->getEsncard(),'esncard')) {
            throw new DuplicatedValueException('esncard');
        }
        if ($this->existsResource($resource->getEmail(),'email')) {
            throw new DuplicatedValueException('email');
        }

        $resource->setModAt();
        $resource->setExpiresAt();

        return parent::postResource($resource);
    }

    public function updateResource($resource)
    {
        if ($this->existsResource($resource->getEsncard(),'esncard',$resource->getId())) {
            throw new DuplicatedValueException('esncard');
        }
        if ($this->existsResource($resource->getEmail(),'email',$resource->getId())) {
            throw new DuplicatedValueException('email');
        }
        $resource->setModAt();
        $resource->setExpiresAt();

        return parent::updateResource($resource);
    }
}
