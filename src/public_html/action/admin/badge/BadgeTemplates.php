<?php

class action_admin_badge_BadgeTemplates extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_BadgeTemplates();
		$this->converter = new viewConverter_admin_badge_BadgeTemplates();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent(
			$user, 
			array(
				model_Role::$EVENT_MANAGER,
				model_Role::$EVENT_REGISTRAR
			), 
			$eventId
		);
		
		return $hasRole;
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params); 
		return $this->converter->getView($info);
	}
	
	public function listTemplates() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->listTemplates($params);
		return $this->converter->getListTemplates($info);
	}
	
	public function deleteTemplates() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'templateIds' => RequestUtil::getValueAsArray('templateIds', array())
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->deleteTemplates($params);
		return $this->converter->getDeleteTemplates($info);
	}
	
	public function addTemplate() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'badgeTemplateType' => '',
			'regTypeIds' => array(-1)
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_BadgeTemplate::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->addTemplate($params);
		return $this->converter->getAddTemplate($info);
	}
	
	public function removeTemplate() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeTemplate($params);
		return $this->converter->getRemoveTemplate($info);
	}
	
	public function copyTemplate() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
	
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->copyTemplate($params);
		return $this->converter->getCopyTemplate($info);
	}
}

?>