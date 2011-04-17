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
				$sessionFieldValue = model_reg_Session::getContactField(model_ContentType::$CONTACT_FIELD.'_'.$field['id']);
				
				if($firstTimeOnPage) {
					// user has fill out the field before, so use their previous value. this covers the case
					// where they've changed reg types and need to click through the page again. when that 
					// happens we want to keep the values they've already entered.
					if(!empty($sessionFieldValue)) {
						$values[$field['id']] = $sessionFieldValue;
					}
					// defaults apply to first member in group and subsequent members where the field value is not
					// set to carry over in group reg settings.
					else if($firstRegInGroup || !model_Event::hasGroupRegDefault($this->event, $field)) {
						$values[$field['id']] = model_ContactField::getDefaultValue($field);
					}
				}
				// user has already filled this out, so get value from session.
				else {
					$values[$field['id']] = $sessionFieldValue;
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