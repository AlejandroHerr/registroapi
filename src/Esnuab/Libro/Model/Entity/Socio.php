<?php

namespace Esnuab\Libro\Model\Entity;

class Socio extends Base
{
    protected $id;
    protected $nombre;
    protected $apellido;
    protected $email;
    protected $esncard;
    protected $passport;
    protected $pais;
    protected $created_at;
    protected $expires_at;
    protected $mod_at;
    protected $language;

    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        $this->id=$id;

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
    public function getApellido()
    {
        return $this->apellido;
    }
    public function setApellido($apellido)
    {
        $this->apellido = ucwords($apellido);

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
    public function getEsncard()
    {
        return $this->esncard;
    }
    public function setEsncard($esncard)
    {
        $this->esncard = strtoupper($esncard);

        return $this;
    }
    public function getPassport()
    {
        return $this->passport;
    }
    public function setPassport($passport)
    {
        $this->passport = strtoupper($passport);

        return $this;
    }
    public function getPais()
    {
        return $this->pais;
    }
    public function setPais($pais)
    {
        $this->pais = $pais;

        return $this;
    }
    public function getCreatedAt()
    {
        return $this->created_at;
    }
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }
    public function getExpiresAt()
    {
        return $this->expires_at;
    }
    public function setExpiresAt()
    {
        $time = new \DateTime();
        $time = \DateTime::createFromFormat('Y-m-d', $this->getCreatedAt());
        $time->modify("+1 year");
        $this->expires_at = $time->format('Y-m-d');

        return $this;
    }
    public function getModAt()
    {
        return $this->mod_at;
    }
    public function setModAt()
    {
        $time = new \DateTime(date('Y-m-d H:i:s', time()));
        $this->mod_at = $time->format('Y-m-d');

        return $this;
    }
    public function setExpired()
    {
        $time = new \DateTime(date('Y-m-d H:i:s', time()));
        $this->expires_at = $time->format('Y-m-d');

        return $this;
    }
    public function getLanguage()
    {
        return $this->language;
    }
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }
}
