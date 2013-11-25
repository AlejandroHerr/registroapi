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
        $sucio= $app['db']->fetchAssoc('SELECT * FROM socio WHERE id = ?', array($app->escape($id)));
		if($sucio){
			$socio = new Socio();
			$socio->__construct($sucio);
        	return $socio->serialize();
        }
        $error = array(
        	'error' =>array(
        		'code' => '400',
        		'mensaje' => 'El socio no existe!'
        ));
        return new Response(json_encode($error), 400);

    }
    function createSocio($socio,$app){
    	$socio->setModAt();
		$socio->setExpiresAt();
    	$app['db']->insert('socio',$socio->toArray());
    	return $socio;

    }
}