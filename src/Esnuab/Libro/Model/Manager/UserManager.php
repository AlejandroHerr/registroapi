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
    public function createUser(User $user)
    {
        $user->setActivo(1);
        $user->setBlocked(0);
        $this->conn->insert('users',$user->toArray());

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
    public function getCount($queryParams)
    {
        $query = 'SELECT COUNT(id) AS total FROM users';
        if ($queryParams['active']==1) {
            $query .= ' WHERE activo=1';
        }
        $count=$this->conn->fetchAssoc($query);

        return $count['total'];
    }
    public function getUser($id)
    {
        $user = $this->conn->fetchAssoc('SELECT id,username,roles,activo,blocked,nombre,apellidos,email FROM users WHERE id = ?', array($app->escape($id)));

        return new User($user);
    }
    public function getUsers($queryParams)
    {
        $offset = ($queryParams['page']-1)*$queryParams['maxResults'];
        $query = 'SELECT id,username,roles,activo,blocked,nombre,apellidos,email from users';
        if ($queryParams['active']==1) {
            $query .= ' WHERE activo=1';
        }
        $query .= '  ORDER BY username ASC';
        $query .= ' LIMIT '.$offset.','.$queryParams['maxResults'];
        $users=$this->conn->fetchAll($query);

        return $users;
    }
    public function isUserBlocked($id)
    {
        $user=$this->getUser($id);

        return $user->getBlocked();
    }
    public function updateUser($user,$id)
    {
        $this->conn->update('users',$user->toArray(),array('id' => $id));
        $user->setId($id);

        return $user;
    }
}
