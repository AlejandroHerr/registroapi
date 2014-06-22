<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\DuplicatedValueException;
use AlejandroHerr\ApiApplication\Model\Manager\AbstractDbalManager;
use Esnuab\Libro\Model\Entity\Socio;
use Silex\Application;

class SocioManager extends AbstractDbalManager
{
    protected $entity = 'Esnuab\Libro\Model\Entity\Socio';
    protected $table = 'socio';

    protected function beforeGetResources(Application $app, $queryParameters)
    {
        $offset=($queryParameters['currentPage']-1)*$queryParameters['maxResults'];

        return 'ORDER BY '.$queryParameters['orderBy'].' '.$queryParameters['orderDir'].
            ' LIMIT '.$offset.','.$queryParameters['maxResults'];
    }

    protected function beforePostResource(Application $app, $resource)
    {
        if ($this->existsResource($app,$resource->getEsncard(),'esncard')) {
            throw new DuplicatedValueException('esncard');
        }
        if ($this->existsResource($app,$resource->getEmail(),'email')) {
            throw new DuplicatedValueException('email');
        }
        $resource->setModAt();
        $resource->setExpiresAt();
    }

    protected function beforePutResource(Application $app, $resource)
    {
        if ($this->existsResource($app,$resource->getEsncard(),'esncard',$resource->getId())) {
            throw new DuplicatedValueException('esncard');
        }
        if ($this->existsResource($app,$resource->getEmail(),'email',$resource->getId())) {
            throw new DuplicatedValueException('email');
        }
        $resource->setModAt();
        $resource->setExpiresAt();
    }
}
