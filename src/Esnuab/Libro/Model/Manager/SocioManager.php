<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\DuplicatedValueException;
use AlejandroHerr\ApiApplication\Model\Manager\AbstractDbalManager;
use Esnuab\Libro\Model\Entity\Socio;
use Silex\Application;

class SocioManager extends AbstractDbalManager
{
    protected $entity = 'Esnuab\Libro\Model\Entity\Socio';
    protected $collection = 'Esnuab\Libro\Model\Entity\SocioCollection';
    protected $table = 'socio';

    public function afterPostResource(Application $app, $resource)
    {
        $confirmation = array(
            'userId' => $resource->getId(),
            'name' => $resource->getName().' '.$resource->getSurname(),
            'email' => $resource->getEmail(),
            'esncard' => $resource->getEsncard(),
            'language' => $resource->getLanguage(),
            'expires_at' => $resource->getExpiresAt()
        );

        $app['db']->insert(
            'socio_confirmation',
            $confirmation
        );
    }

    public function beforeGetCollection(Application $app, $queryParameters)
    {
        $queryParameters = array_map(array($app,'escape'), $queryParameters);
        $offset=($queryParameters['page']-1)*$queryParameters['max'];

        return 'ORDER BY '.$queryParameters['by'].' '.$queryParameters['dir'].
            ' LIMIT '.$offset.','.$queryParameters['max'];
    }

    public function beforePostResource(Application $app, $resource)
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

    public function beforePutResource(Application $app, $resource)
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
