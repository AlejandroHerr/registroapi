<?php

namespace AlejandroHerr\ApiApplication\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\ResourceDoesNotExistException;
use Silex\Application;

abstract class AbstractDbalManager
{
    protected $entity;
    protected $table;
    protected $collection;

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

    public function getResourceById(Application $app, $id)
    {
        if (!($resource = $app['db']->fetchAssoc('SELECT * FROM '.$this->table.' WHERE id = ?', array($app->escape($id))))) {
            throw new ResourceDoesNotExistException($id);
        }

        return new $this->entity($resource);
    }

    public function getCollection(Application $app, $query)
    {
        $query = 'SELECT * from '.$this->table.' '.$query;
        $collection = new $this->collection($app['db']->fetchAll($query));

        return $collection;
    }

    public function postResource(Application $app, $resource)
    {
        $app['db']->insert(
            $this->table,
            $resource->toArray()
        );
        $resource->setId($app['db']->lastInsertId());
    }

    public function putResource(Application $app, $resource)
    {
        $app['db']->update(
            $this->table,
            $resource->toArray(),
            array('id' => $resource->getId())
        );
    }

    protected function existsResource(Application $app, $value, $field='id', $excludedId=null)
    {
        $query = 'SELECT * FROM '.$this->table;
        $query .= ' WHERE '.$field.' = "'.$app->escape($value).'"';
        $query .= ($excludedId === null) ? '' : ' AND id != '.$app->escape($excludedId);

        return $app['db']->fetchAssoc($query) ? true : false;
    }
}
