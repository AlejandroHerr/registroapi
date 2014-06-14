<?php

namespace Esnuab\Libro\Model\Entity;

class User extends Base
{
    protected $id;
    protected $username;
    protected $password;
    protected $roles;
    protected $activo;
    protected $blocked;
    protected $nombre;
    protected $apellidos;
    protected $email;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id=$id;

        return $this;
    }
    public function getUsername()
    {
        return $this->username;
    }
    public function setUsername($username)
    {
        $this->username=$username;

        return $this;
    }
    public function setPassword($password)
    {
        $this->password=base64_encode(hash('sha512',$password, true));

        return $this;
    }
    public function getActivo()
    {
        return $this->activo;
    }
    public function setActivo($activo)
    {
        $this->activo=$activo;

        return $this;
    }
    public function getNombre()
    {
        return $this->nombre;
    }
    public function setNombre($nombre)
    {
        $this->nombre = ucwords($nombre);

        return $this;
    }
    public function getApellidos()
    {
        return $this->apellidos;
    }
    public function setApellidos($apellidos)
    {
        $this->apellidos = ucwords($apellidos);

        return $this;
    }
    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }
    public function getPassword()
    {
        return $this->password;
    }
    public function getRoles()
    {
        return $this->roles;
    }
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }
    public function getBlocked()
    {
        return $this->blocked;
    }
    public function setBlocked($blocked)
    {
        $this->blocked = $blocked;

        return $this;
    }
}
