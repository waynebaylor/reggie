<?php

class fragment_reg_Section extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
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
		$regTypeId = model_reg_Session::getRegType();
		
		if(model_Section::containsRegTypes($this->section)) {
			$regTypes = new fragment_reg_RegTypes($this->section['content']);
			return $regTypes->html();
		}
		else if(model_Section::containsContactFields($this->section)) {
			$completedPageIds = model_reg_Session::getCompletedPages();
			$firstTimeOnPage = !in_array($this->section['pageId'], $completedPageIds);
			$firstRegInGroup = model_reg_Session::getCurrent() === 0;
			
			$values = array();
			foreach($this->section['content'] as $field) {
				// defaults apply to first member in group and subsequent members where the field value is not
				// set to carry over in group reg settings.
				if($firstTimeOnPage && $firstRegInGroup) { 
					// user hasn't filled this out, so use defaults (if any).
					$values[$field['id']] = model_ContactField::getDefaultValue($field);
				}
				else if($firstTimeOnPage && !model_Event::hasGroupRegDefault($this->event, $field)) {
					// first time on page for additional member in reg group AND field value from first
					// member in group is not set to carry over.
					$values[$field['id']] = model_ContactField::getDefaultValue($field);
				}
				else {
					// user has already filled this out, so get value from session.
					$values[$field['id']] = model_reg_Session::getContactField(model_ContentType::$CONTACT_FIELD.'_'.$field['id']);
				}
			}
			
			$fields = new fragment_reg_ContactFields($this->section, $regTypeId, $values);
			return $fields->html();	
		}
		else if(model_Section::containsRegOptions($this->section)) {
			$selectedOpts = model_reg_Session::getRegOptions();

			$groups = new fragment_reg_regOptionGroup_RegOptionGroups($this->section['content'], $regTypeId, $selectedOpts, $this->section['pageId']);
			return $groups->html();
		}
		else if(model_Section::containsVariableQuantityOptions($this->section)) {
			$options = new fragment_reg_regOption_VariableQuantityOptions($this->section['content']);
			return $options->html();
		}
	}
}

?>