<?php

namespace AlejandroHerr\ApiApplication\TaskScheduler;

interface TaskSchedulerInterface
{
    public function addTask($type, $action, $entity);
}
