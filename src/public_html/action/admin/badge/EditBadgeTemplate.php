<?php

class action_admin_badge_EditBadgeTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_badge_EditBadgeTemplate();
		$this->converter = new viewConverter_admin_badge_EditBadgeTemplate();
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'selectedCellId' => 0
		));
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function addBadgeCell() {
		$params = RequestUtil::getValues(array(
			'badgeTemplateId' => 0,
			'contentType' => 'text',
			'contactFieldId' => 0,
			'text' => '',
		));
		
		$info = $this->logic->addBadgeCell($params); 
		return $this->converter->getAddBadgeCell($info);
	}
	
	public function saveTemplate() {
		$errors = validation_Validator::validate(validation_admin_BadgeTemplate::getConfig(), array(
			'name' => RequestUtil::getValue('name', ''),
			'regTypeIds' => RequestUtil::getValueAsArray('regTypeIds', array(-1))
		));
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'name' => '',
			'regTypeIds' => array(-1)
		));
		
		$info = $this->logic->saveTemplate($params);
		return $this->converter->getSaveTemplate($info);
	}
	
	public function saveCellDetails() {
		$params = RequestUtil::getValues(array(
			'id' => 0,
			'xCoord' => 0,
			'yCoord' => 0,
			'width' => 4,
			'font' => 'helvetica',
			'fontSize' => 12,
			'horizontalAlign' => 'C'
		));
		
		$info = $this->logic->saveCellDetails($params);
		return $this->converter->getSaveCellDetails($info);
	}
	
	public function addCellContent() {
		
	}
	
	public function moveCellContentUp() {
		
	}
	
	public function moveCellContentDown() {
		
	}
}

?>