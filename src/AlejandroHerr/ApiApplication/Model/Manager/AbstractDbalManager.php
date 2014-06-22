<?php

namespace AlejandroHerr\ApiApplication\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\ResourceDoesNotExistException;
use Silex\Application;

abstract class AbstractDbalManager
{
    protected $entity;
    protected $table;

    public function deleteResource(Application $app, $id)
    {
        if (!$app['db']->delete($this->table,array('id' => $app->escape($id)))) {
            throw new ResourceDoesNotExistException($id);
        }
    }

    public function getCount(Application $app, $condition = '')
    {
        $query = 'SELECT COUNT(id) AS total FROM '.$this->table.' '.$condition;
        $count = $app['db']->fetchAssoc($query);

        return $count['total'];
    }

    public function getResource(Application $app, $id)
    {
        if (!($ressource = $app['db']->fetchAssoc('SELECT * FROM '.$this->table.' WHERE id = ?', array($app->escape($id))))) {
            throw new ResourceDoesNotExistException($id);
        }

        return new $this->entity($ressource);
    }

    public function getResources(Application $app, $queryParameters = array())
    {
        $query = method_exists($this, 'beforeGetResources') ? $this->beforeGetResources($app, $queryParameters) : '';

        $query = 'SELECT * from '.$this->table.' '.$query;
        $resources = $app['db']->fetchAll($query);

        !method_exists($this, 'afterGetResources') ?: $this->afterGetResources($app, $queryParameters);

        return $resources;
    }

    public function postResource(Application $app, $resource)
    {
        !method_exists($this, 'beforePostResource') ?: $this->beforePostResource($app, $resource);

        $app['db']->insert(
            $this->table,
            $resource->toArray()
        );

        !method_exists($this, 'afterPostResource') ?: $this->afterPostResource($app, $resource);
    }

    public function putResource(Application $app, $resource)
    {
        !method_exists($this, 'beforePutResource') ?: $this->beforePutResource($app, $resource);

        $app['db']->update(
            $this->table,
            $resource->toArray(),
            array('id' => $resource->getId())
        );

        !method_exists($this, 'afterPutResource') ?: $this->afterPutResource($app, $resource);
    }

    protected function existsResource(Application $app, $value, $field='id', $excludedId=null)
    {
        $query = 'SELECT * FROM '.$this->table;
        $query .= ' WHERE '.$field.' = "'.$app->escape($value).'"';
        $query .= ($excludedId === null) ? '' : ' AND id != '.$app->escape($excludedId);

        return $app['db']->fetchAssoc($query) ? true : false;
    }
}
