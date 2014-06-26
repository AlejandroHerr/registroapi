<?php

namespace AlejandroHerr\AuditLog\Formatter;

use Monolog\Formatter\FormatterInterface;

class AuditFormatter implements FormatterInterface
{
    public function format(array $record)
    {
        $record['extra'] = array_merge(
            array('channel' => $record['channel']),
            array('level' => $record['level']),
            array('level_name' => $record['level_name']),
            $record['extra']
        );
        $formatted = array(
            'context' => json_encode($record['context']),
            'extra' => json_encode($record['extra'])
        );

        return $formatted;
    }

    public function formatBatch(array $records)
    {
        return json_encode($records);
    }
}
