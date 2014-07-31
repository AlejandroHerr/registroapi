<?php

namespace AlejandroHerr\AuditLog\Handler;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Doctrine\DBAL\Connection;

class DbalHandler extends AbstractProcessingHandler
{
    private $initialized = false;
    private $conn;

    public function __construct(Connection $conn, $level = Logger::DEBUG, $bubble = true)
    {
        $this->conn = $conn;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record)
    {
        if (!$this->initialized) {
            $this->initialize();
        }
        $this->conn->insert('monolog', array(
            'channel' => $record['channel'],
            'level' => $record['level'],
            'user' => $record['extra']['user'],
            'entity' => $record['context']['id'],
            'message' => $record['message'],
            'context' => $record['formatted']['context'],
            'extra' => $record['formatted']['extra'],
            'time' => $record['datetime']->format('U')
        ));
    }

    private function initialize()
    {
        $this->conn->executeQuery('CREATE TABLE IF NOT EXISTS monolog ' . '(id int(10) NOT NULL AUTO_INCREMENT, channel VARCHAR(255), level INTEGER, message LONGTEXT, time INTEGER UNSIGNED, PRIMARY KEY (`id`))');
        $this->initialized = true;
    }
}
