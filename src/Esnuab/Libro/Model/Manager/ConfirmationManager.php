<?php

namespace Esnuab\Libro\Model\Manager;

use Silex\Application;

class ConfirmationManager
{
	function getUnconfirmed(Application $app)
	{
		$query = 'SELECT id,userId,name,email,expires_at,esncard from socio_confirmation WHERE confirmed = 0';
		$unconfirmed=$app['db']->fetchAll($query);
		return $this->addSubjectType($unconfirmed);
	}
	function getMergeVars($subjects)
	{
		$mergeVars=array();
		foreach ($subjects as $value) {
			$mergeVars[]=array(
				'rcpt' => $value['email'],
				'vars' => array(
					array(
						'name' => 'NAME',
						'content' => $value['name']
					),
					array(
						'name' => 'EXPIREDATE',
						'content' => $value['expires_at']
					),
					array(
						'name' => 'ESNCARD',
						'content' => $value['esncard']
					)
				)
			);
		}
		return $mergeVars;
	}
	function addSubjectType($unconfirmed)
	{
		foreach ($unconfirmed as &$value) {
			$value['type'] = 'bcc';
		}
		return $unconfirmed;
	}
	function recordConfirmations(Application $app,$confirmed,$notConfirmed)
	{
		$notConfirmedEmails=array();
		foreach ($notConfirmed as $value) {
			$notConfirmedEmails[]=$value['email'];
		}
		foreach ($confirmed as $key => &$value) {
			if(in_array($value['email'], $notConfirmedEmails)){
				unset($confirmed[$key]);
			}
		}
		$this->confirm($app,$confirmed);
		$this->trackError($app,$notConfirmed);
	}
	function confirm(Application $app,$confirmed)
	{
		foreach ($confirmed as $value) {
			$app['db']->update('socio_confirmation', array('confirmed' => 1), array('email' => $value['email']));
		}
	}
	function trackError(Application $app,$unconfirmed)
	{
		foreach ($unconfirmed as $value) {
			$app['db']->update('socio_confirmation', array('error_flag' => 1,'error'=>$value['error']), array('email' => $value['email']));
		}
	}
	function deleteConfirmed(Application $app)
	{
		$query = 'SELECT id,userId,name,email,expires_at,esncard from socio_confirmation WHERE confirmed = 0';
		$app['db']->delete('socio_confirmation',array('confirmed' => 1));
	}
}