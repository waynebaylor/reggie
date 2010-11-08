<?php

class action_admin_regOption_RegOptionGroup extends action_BaseAction
{
	private $optionManager;
	private $optionGroupManager;
	
	function __construct() {
		parent::__construct();
		
		$this->optionManager = db_RegOptionManager::getInstance();
		$this->optionGroupManager = db_RegOptionGroupManager::getInstance();
	}
	
	public function view() {
		$id = $_REQUEST['id'];
		$group = $this->optionGroupManager->find($id);
		
		if(empty($group)) {
			return new template_ErrorPage();
		}
		
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new template_admin_EditSectionRegOptionGroup($event, $group);
	}
	
	public function addGroup() {
		$id = $_REQUEST['regOptionId'];
		$option = $this->optionManager->find($id);
		
		if(empty($option)) {
			return new fragment_AjaxError();
		}

		$description = $_REQUEST['description'];
		
		$required = isset($_REQUEST['required'])? $_REQUEST['required'][0] : 'false';
		$required = ($required === 'true')? 'true' : 'false';
		
		$multiple = isset($_REQUEST['multiple'])? $_REQUEST['multiple'][0] : 'false';
		$multiple = ($multiple === 'true')? 'true' : 'false';

		$minimum = ($multiple === 'true')? $_REQUEST['minimum'] : 0;
		$maximum = ($multiple === 'true')? $_REQUEST['maximum'] : 0;
		
		$group = array(
			'regOptionId' => $option['id'],
			'description' => $description,
			'required' => $required,
			'multiple' => $multiple,
			'minimum' => $minimum,
			'maximum' => $maximum
		);
		
		$this->optionGroupManager->createGroup($group);
		
		$option = $this->optionManager->find($id);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function removeGroup() {
		$id = $_REQUEST['id'];
		$group = $this->optionGroupManager->find($id);
		
		if(empty($group)) {
			return new fragment_AjaxError();
		}
		
		$this->optionGroupManager->delete($group);
		
		$option = $this->optionManager->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function moveGroupUp() {
		$id = $_REQUEST['id'];
		$group = $this->optionGroupManager->find($id);
		
		if(empty($group)) {
			return new fragment_AjaxError();
		}
		
		$this->optionGroupManager->moveGroupUp($group);
		
		$option = $this->optionManager->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function moveGroupDown() {
		$id = $_REQUEST['id'];
		$group = $this->optionGroupManager->find($id);
		
		if(empty($group)) {
			return new fragment_AjaxError();
		}
		
		$this->optionGroupManager->moveGroupDown($group);
		
		$option = $this->optionManager->find($group['regOptionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionGroup_List($event, $option);
	}
	
	public function saveGroup() {
		$id = $_REQUEST['id'];
		$group = $this->optionGroupManager->find($id);
		
		if(empty($group)) {
			return new fragment_AjaxError();
		}
		
		$group = array(
			'id' => $_REQUEST['id'],
			'description' => $_REQUEST['description'],
			'required' => isset($_REQUEST['required'])? $_REQUEST['required'][0] : 'false',
			'multiple' => isset($_REQUEST['multiple'])? $_REQUEST['multiple'][0] : 'false',
			'minimum' => $_REQUEST['minimum'],
			'maximum' => $_REQUEST['maximum']
		);
		
		$group['required'] = ($group['required'] === 'true')? 'true' : 'false';
		$group['multiple'] = ($group['multiple'] === 'true')? 'true' : 'false';
		
		$group['minimum'] = $group['multiple'] === 'true'? intval($group['minimum'], 10) : 0;
		$group['maximum'] = $group['multiple'] === 'true'? intval($group['maximum'], 10) : 0;
		
		$this->optionGroupManager->save($group);
		
		return new fragment_Success();
	}
}

?>