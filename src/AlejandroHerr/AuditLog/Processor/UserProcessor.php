<?php

namespace AlejandroHerr\AuditLog\Processor;

use Silex\Application;

class UserProcessor
{
    protected $context;

    public function __construct(Application $context)
    {
        $this->context = $context;
    }

    public function __invoke(array $record)
    {
        $token = $this->context['security']->getToken();

        $record['extra'] = array_merge(
            $record['extra'],
            array('user' => null !== $token ? $token->getUser()->getUsername() : 'anon')
        );

        return $record;
    }
}
