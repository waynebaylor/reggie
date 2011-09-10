<?php

class action_admin_registration_CreateRegistration extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_registration_CreateRegistration();
		$this->converter = new viewConverter_admin_registration_CreateRegistration();
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function createRegistration() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'categoryId' => 0
		));
		
		$info = $this->logic->createRegistration($params);
		return $this->converter->getCreateRegistration($info);
	}
}

?>