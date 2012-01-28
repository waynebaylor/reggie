<?php

class viewConverter_admin_contactField_Option extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getAddOption($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getRemoveOption($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getMoveOptionUp($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
	
	public function getMoveOptionDown($properties) {
		$this->setProperties($properties);
		return new fragment_contactFieldOption_List($this->event, $this->field);
	}
}

?>