<?php

namespace AlejandroHerr\ApiApplication\TaskScheduler;

use Doctrine\DBAL\Connection;

class DbalTaskScheduler implements TaskSchedulerInterface
{
    protected $conn;
    protected $table;

    public function __construct(Connection $conn, $table = 'tasks')
    {
        $this->conn = $conn;
        $this->table = $table;
    }

    public function addTask($type, $action, $entity)
    {
        $task = new Model\Task();
        $task->setType($type)
            ->setAction($action)
            ->setEntity($entity)
            ->setTimestamp(time());

        $this->conn->insert($this->table, $task->toArray());
    }
}
