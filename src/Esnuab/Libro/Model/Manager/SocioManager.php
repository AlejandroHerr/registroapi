<?php

namespace Esnuab\Libro\Model\Manager;

use Esnuab\Libro\Model\Entity\Socio;

class SocioManager {
	function getSocios($app,$queryOptions) {
		$queryOptions['min'] = ($queryOptions['currentPage']-1)*$queryOptions['maxResults'];
		foreach ($queryOptions as &$value) {
			$value = $app->escape($value);
		}
		$query='ORDER BY '.$queryOptions['orderBy'].' '.$queryOptions['orderDir'].', id '.$queryOptions['orderDir'];
		$query=$query.' LIMIT '.$queryOptions['min'].','.$queryOptions['maxResults'];
		$socios = $app['db']->fetchAll('SELECT * FROM socio '.$query);
		return $socios;
	}
	function getSocio($app,$id) {
		$socio = $app['db']->fetchAssoc('SELECT * FROM socio WHERE id = ?', array($app->escape($id)));
		return new Socio($socio);
	}
	function createSocio($socio,$app){
		$socio->setModAt();
		$socio->setExpiresAt();
		$app['db']->insert('socio',$socio->toArray());
		$socio->setId($app['db']->lastInsertId());
		return $socio;

	}
	function updateSocio($socio,$app,$id){
		$socio->setModAt();
		$socio->setExpiresAt();
		$app['db']->update('socio',$socio->toArray(),array('id' => $app->escape($id)));
		$socio->setId($id);
		return $socio;

	}
	function deleteSocio($app,$id){
		$app['db']->delete('socio',array('id' => $app->escape($id)));
	}
	function existsSocio($app,$id){
		if($app['db']->fetchAssoc('SELECT * FROM socio WHERE id = ?', array($app->escape($id)))){
			return true;
		}
		return false;
	}

	function existsEsncard($app,$esncard,$id='a'){
		if($app['db']->fetchAssoc('SELECT id FROM socio WHERE esncard = ? AND id != ?',array($app->escape($esncard),$app->escape($id)))){
			return true;
		}
		return false;

	}

	function getCount($app){
		$count=$app['db']->fetchAssoc('SELECT COUNT(id) AS total FROM socio');
		return $count['total'];
	}
}