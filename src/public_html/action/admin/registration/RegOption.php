<?php

class action_admin_registration_RegOption extends action_ValidatorAction
{
	public function view() {
		throw new Exception("Action not implemented: view");	
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_registration_Registration();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function addRegOptions() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0,
			'regOpts' => array()
		));
		foreach($params['regOpts'] as $optionId) {
			$params['regOptPrice_'.$optionId] = RequestUtil::getValue('regOptPrice_'.$optionId, 0);	
		}
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addRegOptions($params);
		return $this->converter->getAddRegOptions($info);
	}
	
	public function cancelRegOption() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0, // the Registration_RegOption id.
			'groupId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->cancelRegOption($params);
		return $this->converter->getCancelRegOption($info);		
	}
	
	public function saveVariableQuantity() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'registrationId' => 0
		));
		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				$optId = str_replace(model_ContentType::$VAR_QUANTITY_OPTION.'_', '', $key);
				
				$params[$key] = $value;
				$params['priceId_'.$optId] = RequestUtil::getValue('priceId_'.$optId, 0); 
			}
		}
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveVairableQuantity($params);
		return $this->converter->getSaveVariableQuantity($info);
	}
}

?>