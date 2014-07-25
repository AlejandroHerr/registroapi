<?php

namespace AlejandroHerr\ApiApplication\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Exception\ResourceDoesNotExistException;
use Doctrine\DBAL\Connection;

abstract class AbstractDbalManager
{
    protected $entity;
    protected $table;
    protected $collection;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function deleteResource($id)
    {
        if (!$this->conn->delete($this->table,array('id' => $this->escape($id)))) {
            throw new ResourceDoesNotExistException($id);
        }
    }

    public function getCount($condition = '')
    {
        $query = 'SELECT COUNT(id) AS total FROM '.$this->table.' '.$condition;
        $count = $this->conn->fetchAssoc($query);

        return $count['total'];
    }

    public function getResourceById($id)
    {
        if (!($resource = $this->conn->fetchAssoc('SELECT * FROM '.$this->table.' WHERE id = ?', array($this->escape($id))))) {
            throw new ResourceDoesNotExistException($id);
        }

        return new $this->entity($resource);
    }

    public function getCollection($query)
    {
        $query = 'SELECT * from '.$this->table.' '.$query;
        $collection = new $this->collection($this->conn->fetchAll($query));

        return $collection;
    }

    public function postResource($resource)
    {
        $this->conn->insert(
            $this->table,
            $resource->toArray()
        );
        $resource->setId($this->conn->lastInsertId());

        return $resource;
    }

    public function putResource($resource)
    {
        $this->conn->update(
            $this->table,
            $resource->toArray(),
            array('id' => $resource->getId())
        );

        return $resource;
    }

    protected function escape($text)
    {
        return htmlspecialchars($text, ENT_COMPAT, null, true);
    }

    protected function existsResource($value, $field='id', $excludedId=null)
    {
        $query = 'SELECT * FROM '.$this->table;
        $query .= ' WHERE '.$field.' = "'.$this->escape($value).'"';
        $query .= ($excludedId === null) ? '' : ' AND id != '.$this->escape($excludedId);

        return $this->conn->fetchAssoc($query) ? true : false;
    }
}
