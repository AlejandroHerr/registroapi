<?php

namespace AlejandroHerr\BaseModel\Manager;

use AlejandroHerr\BaseModel\Collection\SimpleCollection;
use AlejandroHerr\BaseModel\Exception\ResourceDoesNotExistException;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;

abstract class AbstractDbalManager implements AbstractManagerInterface
{
    protected $collection;
    protected $conn;
    protected $entity;
    protected $logger;
    protected $table;

    public function __construct(Connection $conn, $model, LoggerInterface $logger = null)
    {
        $this->collection = $model['collection'];
        $this->conn = $conn;
        $this->entity = $model['entity'];
        $this->logger = $logger;
        $this->table = $model['table'];
    }

    public function deleteResource($id)
    {
        if (!$this->conn->delete($this->table,array('id' => $this->escape($id)))) {
            throw new ResourceDoesNotExistException($id);
        }
        if (null !== $this->logger) {
            $this->logger->addWarning(
                sprintf('%s deleted',$this->table),
                array('id' => $id)
            );
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
        $collection = new SimpleCollection($this->entity, $this->conn->fetchAll($query));

        return $collection;
    }

    public function postResource($resource)
    {
        $this->conn->insert(
            $this->table,
            $resource->toArray()
        );
        $resource->setId($this->conn->lastInsertId());

        if (null !== $this->logger) {
            $this->logger->addNotice(
                sprintf('%s created',$this->table),
                $resource->toArray()
            );
        }

        return $resource;
    }

    public function updateResource($resource)
    {
        $this->conn->update(
            $this->table,
            $resource->toArray(),
            array('id' => $resource->getId())
        );

        if (null !== $this->logger) {
            $this->logger->addWarning(
                sprintf('%s updated',$this->table),
                $resource->toArray()
            );
        }

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
