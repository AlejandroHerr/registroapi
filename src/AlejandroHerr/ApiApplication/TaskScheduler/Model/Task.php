<?php

namespace AlejandroHerr\ApiApplication\TaskScheduler\Model;

use AlejandroHerr\BaseModel\Entity\AbstractEntity;

class Task extends AbstractEntity
{
    /**
     * @var int $id the id
     */
    protected $id;
    /**
     * @var string $type the entity type
     */
    protected $type;
    /**
     * @var string $action the action performed
     */
    protected $action;
    /**
     * @var int $entity the id of the entity affected
     */
    protected $entity;
    /**
     * @var bool $doneFlag the job-done-flag
     */
    protected $doneFlag;
    /**
     * @var bool $errorFlag the job-error-flag
     */
    protected $errorFlag;
    /**
     * @var string|null $error the description of the error
     */
    protected $error;
    /**
     * @var bool $warningFlag the job-warning-flag
     */
    protected $warningFlag;
    /**
     * @var string|null $warning the description of the warning
     */
    protected $warning;
    /**
     * @var int $timestamp the timpestamp when the task was created
     */
    protected $timestamp;

    /**
     * Gets the value of id.
     *
     * @return int $id the id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of type.
     *
     * @return string $type the entity type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Sets the value of type.
     *
     * @param string $type the entity type $type the type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Gets the value of action.
     *
     * @return string $action the action performed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Sets the value of action.
     *
     * @param string $action the action performed $action the action
     *
     * @return self
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Gets the value of entity.
     *
     * @return int $entity the id of the entity affected
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Sets the value of entity.
     *
     * @param int $entity the id of the entity affected $entity the entity
     *
     * @return self
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Gets the value of doneFlag.
     *
     * @return bool $doneFlag the job-done-flag
     */
    public function getDoneFlag()
    {
        return $this->doneFlag;
    }

    /**
     * Sets the value of doneFlag.
     *
     * @param bool $doneFlag the job-done-flag $doneFlag the done flag
     *
     * @return self
     */
    public function setDoneFlag($doneFlag)
    {
        $this->doneFlag = $doneFlag;

        return $this;
    }

    /**
     * Gets the value of errorFlag.
     *
     * @return bool $errorFlag the job-error-flag
     */
    public function getErrorFlag()
    {
        return $this->errorFlag;
    }

    /**
     * Sets the value of errorFlag.
     *
     * @param bool $errorFlag the job-error-flag $errorFlag the error flag
     *
     * @return self
     */
    public function setErrorFlag($errorFlag)
    {
        $this->errorFlag = $errorFlag;

        return $this;
    }

    /**
     * Gets the value of error.
     *
     * @return string|null $error the description of the error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Sets the value of error.
     *
     * @param string|null $error the description of the error $error the error
     *
     * @return self
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Gets the value of warningFlag.
     *
     * @return bool $warningFlag the job-warning-flag
     */
    public function getWarningFlag()
    {
        return $this->warningFlag;
    }

    /**
     * Sets the value of warningFlag.
     *
     * @param bool $warningFlag the job-warning-flag $warningFlag the warning flag
     *
     * @return self
     */
    public function setWarningFlag($warningFlag)
    {
        $this->warningFlag = $warningFlag;

        return $this;
    }

    /**
     * Gets the value of warning.
     *
     * @return string|null $warning the description of the warning
     */
    public function getWarning()
    {
        return $this->warning;
    }

    /**
     * Sets the value of warning.
     *
     * @param string|null $warning the description of the warning $warning the warning
     *
     * @return self
     */
    public function setWarning($warning)
    {
        $this->warning = $warning;

        return $this;
    }

    /**
     * Gets the value of timestamp.
     *
     * @return int $timestamp the timpestamp when the task was created
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * Sets the value of timestamp.
     *
     * @param int $timestamp the timpestamp when the task was created $timestamp the timestamp
     *
     * @return self
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }
}
