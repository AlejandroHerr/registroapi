<?php

namespace Esnuab\Libro\Model\Entity;


class PrivateUser extends User
{
    protected $password;

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
}
