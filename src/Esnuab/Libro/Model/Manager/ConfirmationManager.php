<?php

namespace Esnuab\Libro\Model\Manager;

use Silex\Application;

class ConfirmationManager
{	
	protected $unconfirmed;
	protected $mergeVars;
	function loadUnconfirmed(Application $app)
	{
		$query = 'SELECT id,userId,name,email,expires_at,esncard from socio_confirmation WHERE confirmed = 0';
		$this->unconfirmed=$app['db']->fetchAll($query);
		$this->setUnconfirmed()->setMergeVars();
		return $this;
	}
	function setMergeVars()
	{
		foreach ($this->unconfirmed as $value) {
			$this->mergeVars[]=array(
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
		return $this;
	}
	function setUnconfirmed()
	{
		foreach ($this->unconfirmed as &$value) {
			$value['type'] = 'bcc';
		}
		return $this;
	}
	function processResult(Application $app,$results)
	{	
		$notConfirmed = array_filter($results, array($this,'filterResult'));
		$confirmed = array_filter($results, array($this,'filterResultInverse'));
		array_walk($notConfirmed,array($this,'setStatus'));
		$this->trackNotConfirmations($app,$notConfirmed);
		$this->trackConfirmations($app,$confirmed);

	}
	function setStatus(&$result)
	{
		if($result['reject_reason']){
			$result['status'] = $result['status'] . ':' . $result['reject_reason'];
		}
		$result['error']=$result['status'];
		unset($result['status']);
		unset($result['_id']);
		unset($result['reject_reason']);
	}
	function filterResult($result)
	{
		return ($result['status']=='rejected' || $result['status']=='invalid');
	}
	function filterResultInverse($result)
	{
		return (!($result['status']=='rejected' || $result['status']=='invalid'));
	}
	function trackNotConfirmations(Application $app,$notConfirmed)
	{
		foreach ($notConfirmed as $value) {
			$app['db']->update('socio_confirmation', array('error_flag' => 1,'error'=>$value['error']), array('email' => $value['email']));
		}
	}
	function trackConfirmations(Application $app,$confirmed)
	{
		foreach ($confirmed as $value) {
			$app['db']->update('socio_confirmation', array('confirmed' => 1), array('email' => $value['email']));
		}
	}
    public function getUnconfirmed()
    {
        return $this->unconfirmed;
    }
    public function getMergeVars()
    {
        return $this->mergeVars;
    }
}