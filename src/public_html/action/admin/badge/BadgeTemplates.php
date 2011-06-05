<?php

class action_admin_badge_BadgeTemplates extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_BadgeTemplates();
		$this->converter = new viewConverter_admin_badge_BadgeTemplates();
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addTemplate() {
		$errors = validation_Validator::validate(validation_admin_BadgeTemplate::getConfig(), array(
			'name' => RequestUtil::getValue('name', ''),
			'regTypeIds' => RequestUtil::getValueAsArray('regTypeIds', array(-1))
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'name' => '',
			'regTypeIds' => array(-1)
		));
		
		$info = $this->logic->addTemplate($params);
		return $this->converter->getAddTemplate($info);
	}
	
	public function removeTemplate() {
		$params = array(
			'id' => RequestUtil::getValue('id', 0)
		);
		
		$info = $this->logic->removeTemplate($params);
		return $this->converter->getRemoveTemplate($info);
	}
}

?>