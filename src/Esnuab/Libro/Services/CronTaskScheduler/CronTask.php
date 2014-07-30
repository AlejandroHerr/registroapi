<?php

namespace Esnuab\Libro\Services\CronTaskScheduler;

use AlejandroHerr\ApiApplication\Model\Entity\AbstractEntity;

class CronTask extends AbstractEntity
{
    protected $id;
    protected $type;
    protected $action;
    protected $entity;
    protected $errorFlag;
    protected $error;
    protected $warningFlag;
    protected $warning;
    protected $timestamp;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function getErrorFlag()
    {
        return $this->errorFlag;
    }

    public function setErrorFlag($errorFlag)
    {
        $this->errorFlag = $errorFlag;

        return $this;
    }

    public function getError()
    {
        return $this->error;
    }

    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    public function getWarningFlag()
    {
        return $this->warningFlag;
    }

    public function setWarningFlag($warningFlag)
    {
        $this->warningFlag = $warningFlag;

        return $this;
    }

    public function getWarning()
    {
        return $this->warning;
    }

    public function setWarning($warning)
    {
        $this->warning = $warning;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
