<?php

namespace Esnuab\Services\AuditLog\Processor;

use Silex\Application;

class RequestProcessor
{
    protected $context;
    
    public function __construct(Application $context)
    {
        $this->context = $context;
    }
    
    public function __invoke(array $record)
    {
        $record['extra'] = array_merge($record['extra'], array(
            'request' => array(
                'url' => $this->context['request']->getUri(),
                'ip' => $this->context['request']->getClientIp(),
                'http_method' => $this->context['request']->getMethod(),
                'referer' => $this->context['request']->headers->get('Referer')
            )
        ));
        return $record;
    }
}
