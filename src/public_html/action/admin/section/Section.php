<?php

class action_admin_section_Section extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_section_Section();
		$this->converter = new viewConverter_admin_section_Section();
	}
	
	public static function checkRole($user, $eventId=0, $method='') {
		return action_admin_event_EditEvent::checkRole($user, $eventId, $method);	
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveSection() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'eventId' => 0,
			'name' => '',
			'text' => '',
			'numbered' => 'F'
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Section::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->saveSection($params);
		return $this->converter->getSaveSection($info);
	}
	
	public function addSection() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'pageId' => 0,
			'contentTypeId' => 0,
			'name' => ''
		));
		
		$user = SessionUtil::getUser();
		self::checkRole($user, $params['eventId']);
		
		$errors = validation_Validator::validate(validation_admin_Section::getConfig(), $params);
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$info = $this->logic->addSection($params);
		return $this->converter->getAddSection($info);
	}

	
	
	
	public function removeSection() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->delete($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionUp() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->moveSectionUp($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}

	public function moveSectionDown() {
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['id']);

		db_PageSectionManager::getInstance()->moveSectionDown($section);

		$page = db_PageManager::getInstance()->find($section['pageId']);

		return new fragment_section_List($page);
	}
}

?>