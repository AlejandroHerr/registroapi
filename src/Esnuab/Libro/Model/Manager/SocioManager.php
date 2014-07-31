<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\DuplicatedValueException;
use AlejandroHerr\ApiApplication\Model\Manager\AbstractDbalManager;
use Esnuab\Libro\Model\Entity\Socio;

class SocioManager extends AbstractDbalManager
{
    protected $entity = 'Esnuab\Libro\Model\Entity\Socio';
    protected $collection = 'Esnuab\Libro\Model\Entity\SocioCollection';
    protected $table = 'socio';

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
