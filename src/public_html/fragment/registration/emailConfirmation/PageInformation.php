<?php

class fragment_registration_emailConfirmation_PageInformation extends template_Template
{
	private $page;
	private $registration;
	
	function __construct($page, $reg) {
		parent::__construct();
		
		$this->page = $page;
		$this->registration = $reg;
	}
	
	public function html() {
		$rows = $this->getInformationRows($this->page, $this->registration);
		
		if(!StringUtil::isBlank($rows)) {
			return <<<_
				<tr><td colspan="2">
					<br/>
					<span style="font-size:20px;">
						{$this->page['title']}
					</span>
				</td></tr>
	
				{$rows}
_;
		}
		
		return '';
	}
	
	private function getInformationRows($page, $registration) {
		$html = '';
		
		foreach($page['sections'] as $section) {
			if(model_Section::containsContactFields($section)) {
				$html .= $this->getSectionInformationRows($section, $registration);
			}
		}
		
		return $html;
	}
	
	private function getSectionInformationRows($section, $registration) {
		$html = '';
		
		foreach($section['content'] as $field) {
			if(model_ContactField::isVisibleTo($field, array('id' => $registration['regTypeId']))) {
				$value = model_Registrant::getInformationValue($registration, $field);
			
				// option fields may have multiple values selected.
				if(model_FormInput::isOptionInput($field['formInput']['id'])) {
					$value = $this->getOptionFieldValue($field, $value);
				}

				// only display required and non-empty fields.
				if(model_ContactField::isRequired($field) || !StringUtil::isBlank($value)) {
					$html .= <<<_
						<tr>
							<td style="font-weight:bold; vertical-align:top;">{$field['displayName']}</td>
							<td style="vertical-align:top;">{$value}</td>
						</tr>
_;
				}
			}
		}
		
		return $html;
	}
	
	private function getOptionFieldValue($field, $value) {
		// the value for fields that can have multiple values 
		// (select and checkbox) will be an array.
		if(is_array($value)) {
			$optionNames = array();
			foreach($value as $optionId) {
				$option = db_ContactFieldOptionManager::getInstance()->find($optionId);
				$optionNames[] = $option['displayName'];
			}
			$value = implode('<br/>', $optionNames);
		}
		else if(!StringUtil::isBlank($value)) {
			$option = db_ContactFieldOptionManager::getInstance()->find($value);
			$value = $option['displayName'];
		}
		
		return $value;
	}
}

?>