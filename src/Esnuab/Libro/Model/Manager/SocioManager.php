<?php

namespace Esnuab\Libro\Model\Manager;

use Esnuab\Libro\Model\Entity\Socio;

class SocioManager {
	function getSocios($app,$params) {
		$offset=($params['currentPage']-1)*$params['maxResults'];
		
		$query = 'SELECT * from socio '.
			'ORDER BY '.$params['orderBy'].' '.$params['orderDir'].
			' LIMIT '.$offset.','.$params['maxResults']
		;
		$socios=$app['db']->fetchAll($query);
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

	function existsSocio($app,$value,$field='id',$excludeId=true,$id=null){
		$query= 'SELECT * FROM socio WHERE '.$field.' = "'.$app->escape($value).'"';
		if(!$excludeId){
			$query = $query . " AND id != ".$app->escape($id); 
		}
		if($app['db']->fetchAssoc($query)){
			return true;
		}
		return false;
	}
	

	function getCount($app){
		$count=$app['db']->fetchAssoc('SELECT COUNT(id) AS total FROM socio');
		return $count['total'];
	}
}