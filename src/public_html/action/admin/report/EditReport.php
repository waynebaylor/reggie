<?php

class action_admin_report_EditReport extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_report_EditReport();
		$this->converter = new viewConverter_admin_report_EditReport();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_report_CreateReport();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'reportId' => 0
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function saveReport() {
		$errors = validation_Validator::validate(validation_admin_Report::getConfig(), array(
			'name' => RequestUtil::getValue('name', '')
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$reportId = RequestUtil::getValue('id', 0);
		$reportName = RequestUtil::getValue('name', '');
		
		$info = $this->logic->saveReport($reportId, $reportName);
		
		return $this->converter->getSaveReport($info);
	}
	
	public function addField() {
		$field = RequestUtil::getValues(array(
			'reportId' => 0, 
			'contactFieldId' => 0
		));
		
		$info = $this->logic->addField($field);
		
		return $this->converter->getAddField($info);
	}
	
	public function removeField() {
		$id = RequestUtil::getValue('id', 0);
		$reportId = RequestUtil::getValue('reportId', 0);
		
		$info = $this->logic->removeField($reportId, $id);
		
		return $this->converter->getRemoveField($info);
	}
	
	public function moveFieldUp() {
		$fieldId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->moveFieldUp($fieldId);
		
		return $this->converter->getMoveFieldUp($info);
	}
	
	public function moveFieldDown() {
		$fieldId = RequestUtil::getValue('id', 0);
		
		$info = $this->logic->moveFieldDown($fieldId);
		
		return $this->converter->getMoveFieldDown($info);
	}
}

?>