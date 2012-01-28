<?php

class viewConverter_admin_registration_RegOption extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getAddRegOptions($properties) {
		$this->setProperties($properties);
		return new fragment_editRegistrations_regOption_List($this->event, $this->registration);
	}
	
	public function getCancelRegOption($properties) {
		$this->setProperties($properties);
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->groupId}");
	}
	
	public function getSaveVariableQuantity($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>