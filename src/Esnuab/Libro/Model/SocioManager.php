<?php

namespace Esnuab\Libro\Model;

use Symfony\Component\HttpFoundation\Response;

use Esnuab\Libro\Model\Entity\Socio;

class SocioManager {
    function getSocios($app) {
        $socios = $app['db']->fetchAll("SELECT * from socio");
        return $socios;
    }
    function getSocio($app,$id) {
        $socio = new Socio();
        $sucio= $app['db']->fetchAssoc('SELECT * FROM socio WHERE id = ?', array($app->escape($id)));
        if(!$sucio){
            return null;
        }
        $socio->__construct($sucio);
        return $socio;
    }
    function createSocio($socio,$app){
        $socio->setModAt();
        $socio->setExpiresAt();
        $app['db']->insert('socio',$socio->toArray());
        return $socio;

    }
    function updateSocio($socio,$app,$id){
        $socio->setModAt();
        $socio->setExpiresAt();
        $app['db']->update('socio',$socio->toArray(),array('id' => $app->escape($id)));
        return $socio;

    }
    function socioExists($app,$id){
        if($app['db']->fetchAssoc('SELECT * FROM socio WHERE id = ?', array($app->escape($id)))){
            return true;
        }
        return false;
    }
}