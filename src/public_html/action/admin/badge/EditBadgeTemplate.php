<?php

class action_admin_badge_EditBadgeTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_EditBadgeTemplate();
		$this->converter = new viewConverter_admin_badge_EditBadgeTemplate();
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
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'selectedCellId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addBadgeCell() {
		$params = RequestUtil::getValues(array(
			'badgeTemplateId' => 0,
			'contentType' => 'text',
			'templateField' => '',
			'text' => '',
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addBadgeCell($params); 
		return $this->converter->getAddBadgeCell($info);
	}
	
	public function saveTemplate() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'name' => '',
			'type' => ''
		));
		$params['regTypeIds'] = RequestUtil::getValueAsArray('regTypeIds', array(-1));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_BadgeTemplate::getConfig(), ArrayUtil::keyIntersect($params, array(
			'name',
			'badgeTemplateType',
			'regTypeIds'
		)));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$info = $this->logic->saveTemplate($params);
		return $this->converter->getSaveTemplate($info);
	}
	
	public function saveCellDetails() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'xCoord' => 0,
			'yCoord' => 0,
			'width' => 4,
			'font' => 'arial',
			'fontSize' => 12,
			'horizontalAlign' => 'C',
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveCellDetails($params); 
		return $this->converter->getSaveCellDetails($info);
	}
	
	public function addCellContent() {
		$params = RequestUtil::getValues(array(
			'cellId' => 0,
			'contentType' => 'text',
			'contactFieldId' => 0,
			'templateField' => 0,
			'text' => '',
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->addCellContent($params);
		return $this->converter->getAddCellContent($info);
	}
	
	public function moveCellContentUp() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveCellContentUp($params);
		return $this->converter->getMoveCellContentUp($info);
	}
	
	public function moveCellContentDown() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveCellContentDown($params);
		return $this->converter->getMoveCellContentDown($info);
	}
	
	public function removeCellContent() {
		$params = RequestUtil::getValues(array(
			'cellId' => 0,
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeCellContent($params);
		return $this->converter->getRemoveCellContent($info);
	}
	
	public function removeBadgeCell() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeBadgeCell($params);
		return $this->converter->getRemoveBadgeCell($info);
	}
}

?>