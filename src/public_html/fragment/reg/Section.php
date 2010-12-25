<?php

class fragment_reg_Section extends template_Template
{
	private $section;
	
	function __construct($section) {
		parent::__construct();
		
		$this->section = $section;
	}
	
	public function html() {
		if(model_Section::containsText($this->section)) {
			return <<<_
				<div class="section-text">
					{$this->section['text']}
				</div>	
				
_;
		}
		else {
			return <<<_
				<div class="section-content">
					{$this->getSectionContent()}
				</div>		
_;
		}
	}
	
	private function getSectionContent() {
		if(model_Section::containsRegTypes($this->section)) {
			$regTypes = new fragment_reg_RegTypes($this->section['content']);
			return $regTypes->html();
		}
		else if(model_Section::containsContactFields($this->section)) {
			$regTypeId = model_reg_Session::getRegType();
			
			// populate with values from the session. 
			$values = array();
			foreach($this->section['content'] as $field) {
				$values[$field['id']] = model_reg_Session::getContactField(model_ContentType::$CONTACT_FIELD.'_'.$field['id']); 
			}
			
			$fields = new fragment_reg_ContactFields($this->section, $regTypeId, $values);
			return $fields->html();	
		}
		else if(model_Section::containsRegOptions($this->section)) {
			$groups = new fragment_reg_regOptionGroup_RegOptionGroups($this->section['content']);
			return $groups->html();
		}
		else if(model_Section::containsVariableQuantityOptions($this->section)) {
			$options = new fragment_reg_regOption_VariableQuantityOptions($this->section['content']);
			return $options->html();
		}
	}
}

?>