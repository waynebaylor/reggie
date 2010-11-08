<?php

class fragment_reg_summary_Information extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$html = '';
		
		$eventFields = model_Event::getInformationFields($this->event);
		foreach($eventFields as $field) {
			if(model_ContactField::isRequired($field)) {
				$name = model_ContentType::$CONTACT_FIELD.'_'.$field['id'];
				$value = model_RegSession::getContactField($name);			
						
				// if the contact field has options, then the value will be
				// the option id.
				if(model_FormInput::isOptionInput($field['formInput']['id'])) {
					if(is_array($value)) {
						$optionNames = '';
						foreach($value as $optionId) {
							$option = db_ContactFieldOptionManager::getInstance()->find($optionId);
							$optionNames .= $option['displayName'].'<br/>';
						}
						$value = $optionNames;	
					}
					else {
						$option = db_ContactFieldOptionManager::getInstance()->find($value);
						$value = $option['displayName'];
					}
				}
					
				$html .= <<<_
				<tr>
					<td class="label">{$field['displayName']}</td>
					<td class="details">{$value}</td>
				</tr>
_;
			}
		}
		
		if(strlen($html) === 0) {
			return '';
		}
		else {
			return <<<_
				{$html}
				<tr>
					<td colspan="2">
						<div class="summary-divider"></div>
					</td>
				</tr>
_;
		}
	}
}

?>