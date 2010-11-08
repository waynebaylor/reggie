<?php

class action_admin_regOption_RegOption extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $_REQUEST['id']);
		
		$eventId = $_REQUEST['eventId'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		return new template_admin_EditSectionRegOption($event, $option);
	}
	
	public function addOption() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		 
		$groupId = $_REQUEST['parentGroupId'];
		$group = $this->getGroup($groupId);
		
		$option = array();
		ObjectUtils::populate($option, $_REQUEST);
		$option['defaultSelected'] = RequestUtil::getValue('defaultSelected', 'false');
		$option['showPrice'] = RequestUtil::getValue('showPrice', 'false');
		
		db_RegOptionManager::getInstance()->createRegOption($option);
		
		$group = $this->getGroup($groupId);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOption_List($event, $group);
	}
	
	public function removeOption() {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $_REQUEST['id']);
		
		db_RegOptionManager::getInstance()->delete($option);
		
		$group = $this->getGroup($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOption_List($event, $group);
	}
	
	public function moveOptionUp() {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $_REQUEST['id']);
		
		db_RegOptionManager::getInstance()->moveOptionUp($option);
		
		$group = $this->getGroup($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOption_List($event, $group);
	}
	
	public function moveOptionDown() {
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $_REQUEST['id']);
		
		db_RegOptionManager::getInstance()->moveOptionDown($option);
		
		$group = $this->getGroup($option['parentGroupId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOption_List($event, $group);
	}
	
	public function saveOption() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$option = RequestUtil::getParameters(array(
			'id',
			'code',
			'description',
			'capacity'
		));
		$option['defaultSelected'] = RequestUtil::getValue('defaultSelected', 'false');
		$option['showPrice'] = RequestUtil::getValue('showPrice', 'false');
		
		db_RegOptionManager::getInstance()->save($option);
		
		return new fragment_Success();
	}
	
	private function getGroup($id) {
		$group = db_SectionRegOptionGroupManager::getInstance()->find($id);
		
		if(empty($group)) {
			// the strict find is here because we don't want the search to end
			// prematurely if the above find doesn't return anything.
			$group = $this->strictFindById(db_RegOptionGroupManager::getInstance(), $id);
		}
		
		return $group;
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'code',
				'value' => $_REQUEST['code'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Code is required.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^[A-Za-z0-9]+$/',
						'text' => 'Code can only contain letters and numbers.'
					)
				)
			),
			array(
				'name' => 'description',
				'value' => $_REQUEST['description'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Description is required.'
					)
				)
			)
		);
	}
} 

?>