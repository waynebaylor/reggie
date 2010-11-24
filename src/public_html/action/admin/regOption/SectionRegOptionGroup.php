<?php

class action_admin_regOption_SectionRegOptionGroup extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$group = $this->strictFindById(db_SectionRegOptionGroupManager::getInstance(), $_REQUEST['id']);
		
		$eventId = $_REQUEST['eventId'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		return new template_admin_EditSectionRegOptionGroup($event, $group);
	}
	
	public function addGroup() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$section = $this->strictFindById(db_PageSectionManager::getInstance(), $_REQUEST['sectionId']);
		
		$group = array(
			'sectionId' => $section['id'],
			'description' => RequestUtil::getValue('description', ''),
			'required' => RequestUtil::getValue('required', 'false'),
			'multiple' => RequestUtil::getValue('multiple', 'false'),
			'minimum' => RequestUtil::getValue('minimum', 0),
			'maximum' => RequestUtil::getValue('maximum', 0)
		);
		
		db_SectionRegOptionGroupManager::getInstance()->createGroup($group);
		
		$section = db_PageSectionManager::getInstance()->find($section['id']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOptionGroup_List($event, $section);
	}
	
	public function removeGroup() {
		$group = $this->strictFindById(db_SectionRegOptionGroupManager::getInstance(), $_REQUEST['id']);
		
		db_SectionRegOptionGroupManager::getInstance()->delete($group);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOptionGroup_List($event, $section);
	}
	
	public function moveGroupUp() {
		$group = $this->strictFindById(db_SectionRegOptionGroupManager::getInstance(), $_REQUEST['id']);
		
		db_SectionRegOptionGroupManager::getInstance()->moveGroupUp($group);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOptionGroup_List($event, $section);
	}
	
	public function moveGroupDown() {
		$group = $this->strictFindById(db_SectionRegOptionGroupManager::getInstance(), $_REQUEST['id']);
		
		db_SectionRegOptionGroupManager::getInstance()->moveGroupDown($group);
		
		$section = db_PageSectionManager::getInstance()->find($group['sectionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_sectionRegOptionGroup_List($event, $section);
	}
	
	public function saveGroup() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);	
		}
		
		$group = $this->strictFindById(db_SectionRegOptionGroupManager::getInstance(), $_REQUEST['id']);
		
		$group['description'] = RequestUtil::getValue('description', '');
		$group['required'] = RequestUtil::getValue('required', 'false');
		$group['multiple'] = RequestUtil::getValue('multiple', 'false');
		$group['minimum'] = RequestUtil::getValue('minimum', 0);
		$group['maximum'] = RequestUtil::getValue('maximum', 0);
		
		db_SectionRegOptionGroupManager::getInstance()->save($group);
		
		return new fragment_Success();
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		$validBooleans = array('true', 'false');
		
		$required = RequestUtil::getValue('required', 'false');
		$multiple = RequestUtil::getValue('multiple', 'false');
		
		if(!in_array($required, $validBooleans)) {
			$required = 'false';
		}
		
		if(!in_array($multiple, $validBooleans)) {
			$multiple = 'false';
		}
		
		if($multiple === 'true') {
			// don't let min/max start with 0, since octal numbers start with 0.
			
			$minimum = RequestUtil::getValue('minimum', 0);
			
			// first check if minimum is valid.
			if(!preg_match('/^0|([1-9][0-9]*)$/', $minimum)) {
				$errors['minimum'] = 'Minimum must be 0 or more.';
			}
			// if required, then the minimum must be at least one.
			else if($required === 'true' && $minimum < 1) {
				$errors['minimum'] = 'Minimum must be 1 or more if Required.';
			}
			// if minimum is greater than 0, then required must be true.
			else if($required === 'false' && $minimum > 0) {
				$errors['minimum'] = 'Minimum must be 0 if not Required.';		
			}
			// maximum can't be less than minimum.
			else {
				$maximum = RequestUtil::getValue('maximum', 0);
				if(!preg_match('/^0|([1-9][0-9]*)$/', $maximum) || $maximum < $minimum) {
					$errors['maximum'] = 'Maximum must be greater or equal to Minimum.';
				}
			}
		}
		
		return $errors;
	}
}

?>