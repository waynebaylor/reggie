<?php

class action_admin_regOption_SectionRegOptionGroup extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_regOption_SectionRegOptionGroup();
		$this->converter = new viewConverter_admin_regOption_SectionRegOptionGroup();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);	
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'sectionId' => 0,
			'required' => 'F',
			'multiple' => 'F',
			'minimum' => 0,
			'maximum' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->addGroup($params);
		return $this->converter->getAddGroup($info);
	}
	
	public function removeGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->removeGroup($params);
		return $this->converter->getRemoveGroup($info);
	}
	
	public function moveGroupUp() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveGroupUp($params);
		return $this->converter->getMoveGroupUp($info);
	}
	
	public function moveGroupDown() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->moveGroupDown($params);
		return $this->converter->getMoveGroupDown($info);
	}
	
	public function saveGroup() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'id' => 0,
			'required' => 'F',
			'multiple' => 'F',
			'minimum' => 0,
			'maximum' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$errors = validation_admin_SectionRegOptionGroup::validate($params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->saveGroup($params);
		return $this->converter->getSaveGroup($info);
	}
}

?>