<?php

namespace Esnuab\AuditLog\Formatter;

use Monolog\Formatter\FormatterInterface;

class AuditFormatter implements FormatterInterface
{
    public function format(array $record)
    {
        $base = array(
            'channel' => $record['channel'],
            'level' => $record['level'],
            'level_name' => $record['level_name']
        );
        $event = array(
            'message' => $record['message'],
            'context' => $record['context']
        );
        foreach ($record['extra'] as $key => $value) {
            $extra[$key] = $value;
        }
        $formatted = array_merge($base, array(
            'event' => $event
        ), $extra);

        return json_encode($formatted);
    }

    public function formatBatch(array $records)
    {
        return json_encode($records);
    }
}
