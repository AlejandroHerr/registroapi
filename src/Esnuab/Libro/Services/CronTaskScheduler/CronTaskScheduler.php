<?php

namespace Esnuab\Libro\Services\CronTaskScheduler;

class CronTaskScheduler
{
    const TYPE_USER = 1;
    const TYPE_SOCIO = 2;
    const ACTION_CREATED = 1;
    const ACTION_UPDATED = 2;
    const ACTION_DELETED = 3;

    protected $conn;
    protected $logger;
    protected $table = 'tasks';

    public function __construct($conn, $logger = null)
    {
        $this->conn = $conn;
        $this->logger = $logger;
    }

    public function addTask($type, $action, $entity)
    {
        $task = new CronTask();
        $task->setType($type)->setAction($action)->setEntity($entity);

        $this->conn->insert(
            $this->table,
            $task->toArray()
        );
    }

    public function addSocioTask($action, $entity)
    {
        $this->addTask(self::TYPE_SOCIO, $action, $entity);
    }

    public function addUserTask($action, $entity)
    {
        $this->addTask(self::TYPE_USER, $action, $entity);
    }
}
