<?php

namespace Esnuab\Libro\Model\Manager;

use Silex\Application;

class ConfirmationManager
{	
	protected $unconfirmed;
	protected $confirmed;
	protected $mergeVars;

	public function deleteConfirmed(Application $app)
	{
		$app['db']->delete(
			'socio_confirmation',
			array(
				'confirmed' => 1,
				'error_flag' => 0
			)
		);
	}
	public function getUnconfirmed()
    {
        return $this->unconfirmed;
    }
    public function getMergeVars()
    {
        return $this->mergeVars;
    }
	public function loadUnconfirmed(Application $app)
	{
		$query = 'SELECT id,userId,name,email,expires_at,esncard,language from socio_confirmation WHERE confirmed = 0';
		$this->unconfirmed=$app['db']->fetchAll($query);
		$this->setUnconfirmed()->setMergeVars();
		return $this;
	}
	public function prepareSubscriptions()
    {
    	array_walk(
    		$this->confirmed,
    		array($this,'getEmails')
    	);
    	$this->unconfirmed=array_filter(
    		$this->unconfirmed,
    		array($this,'isInConfirmed')
    	);	
		$batch=array_map(
			array($this,'mapBatch'),
			$this->unconfirmed
		);
		return $batch;
    }
	public function processResult(Application $app, $results)
	{	
		$notConfirmed = array_filter(
			$results,
			array($this,'filterResult')
		);
		$this->confirmed = array_filter(
			$results,
			array($this,'filterResultInverse')
		);
		array_walk(
			$notConfirmed,
			array($this,'setStatus')
		);
		$this->trackNotConfirmations(
			$app,
			$notConfirmed
		);
		unset($notConfirmed);
		$this->trackConfirmations(
			$app,
			$this->confirmed
		);
	}

    protected function setMergeVars()
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
	protected function setUnconfirmed()
	{
		foreach ($this->unconfirmed as &$value) {
			$value['type'] = 'bcc';
		}
		return $this;
	}
	protected function trackConfirmations(Application $app,$confirmed)
	{
		foreach ($confirmed as $value) {
			$app['db']->update(
				'socio_confirmation',
				array('confirmed' => 1),
				array('email' => $value['email'])
			);
		}
	}
	protected function trackNotConfirmations(Application $app, $notConfirmed)
	{
		foreach ($notConfirmed as $value) {
			$app['db']->update(
				'socio_confirmation',
				array(
					'error_flag' => 1,
					'error'=>$value['error']
				),
				array(
					'email' => $value['email']
				)
			);
		}
	}

	private function filterResult($result)
	{
		return ($result['status']=='rejected' || $result['status']=='invalid');
	}
	private function filterResultInverse($result)
	{
		return (!($result['status']=='rejected' || $result['status']=='invalid'));
	}
	private function getEmails(&$confirmed)
    {
    	$confirmed=$confirmed['email'];
    }
    private function isInConfirmed($users)
    {
    	return (in_array($users['email'], $this->confirmed));
    }
    private function mapBatch($user)
    {
    	$long_name=strrev($user['name']);
    	$long_name=explode(' ', $long_name,2);
    	return array(
    		'email' => array(
    			'email' => $user['email']
    			),
    		'email_type'=>'html',
    		'merge_vars' => array(
    			'MERGE1' => strrev($long_name[1]), 
    			'MERGE2' => strrev($long_name[0]), 
    			'MERGE3' => (($user['language']=='Espanyol') ? 'Español' : 'English')
    			)
    		);

    }
    private function setStatus(&$result)
	{
		if($result['reject_reason']){
			$result['status'] = $result['status'] . ':' . $result['reject_reason'];
		}
		$result['error']=$result['status'];
		unset($result['status']);
		unset($result['_id']);
		unset($result['reject_reason']);
	}	
}