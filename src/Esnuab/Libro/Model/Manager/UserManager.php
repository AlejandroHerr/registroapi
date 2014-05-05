<?php

namespace Esnuab\Libro\Model\Manager;

use Esnuab\Libro\Model\Entity\User;
use Silex\Application;

class UserManager
{
    protected $conn;
    public function __construct($conn)
    {
        $this->conn=$conn;
    }
    public function getUsers()
    {
        $query = 'SELECT id,username,roles,activo,nombre,apellidos,email from users ';
        $users=$this->conn->fetchAll($query);

        return $users;
    }
    public function getUser(Application $app,$id)
    {
        $user = $this->conn->fetchAssoc('SELECT id,username,roles,activo,nombre,apellidos,email FROM users WHERE id = ?', array($app->escape($id)));

        return new User($user);
    }
    public function createUser(User $user)
    {
        $user->setActivo(1);
        $this->conn->insert('users',$user->toArray());

        return $user;
    }
    public function updateSocio($user,$id)
    {
        $this->conn->update('users',$user->toArray(),array('id' => $id));
        $user->setId($id);

        return $user;
    }
    public function existsUser(Application $app,$value,$field='id',$excludeId=true,$id=null)
    {
        $query= 'SELECT id FROM users WHERE '.$field.' = "'.$app->escape($value).'"';
        if (!$excludeId) {
            $query = $query . " AND id != ".$app->escape($id);
        }
        if ($this->conn->fetchAssoc($query)) {
            return true;
        }

        return false;
    }
}
