<?php

namespace Esnuab\Libro\Model\Manager;

use AlejandroHerr\ApiApplication\Model\Manager\AbstractDbalManager;
use Esnuab\Libro\Model\Entity\User;
use Silex\Application;

class UserManager extends AbstractDbalManager
{
    protected $conn;
    protected $entity = 'Esnuab\Libro\Model\Entity\User';
    protected $collection = 'Esnuab\Libro\Model\Entity\UserCollection';
    protected $table = 'users';
    public function __construct($conn)
    {
        $this->conn=$conn;
    }

    public function beforeGetCollection(Application $app, $queryParameters)
    {
        $queryParameters = array_map(array($app,'escape'), $queryParameters);

        $offset=($queryParameters['page']-1)*$queryParameters['max'];

        return 'ORDER BY '.$queryParameters['by'].' '.$queryParameters['dir'].
            ' LIMIT '.$offset.','.$queryParameters['max'];
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

    public function getUser($id)
    {
        $user = $this->conn->fetchAssoc('SELECT * FROM users WHERE id = ?', array($id));

        return new User($user);
    }
    public function getUsers($queryParams)
    {
        $offset = ($queryParams['page']-1)*$queryParams['maxResults'];
        $query = 'SELECT * from users';
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
