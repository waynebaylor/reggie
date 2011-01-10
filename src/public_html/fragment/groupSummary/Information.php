<?php

class fragment_groupSummary_Information extends template_Template
{
	private $event;
	private $registration;
	
	function __construct($event, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
	}
	
	public function html() {
		$html = '';
		
		$regTypeId = $this->registration['regTypeId'];
		
		$eventFields = model_Event::getInformationFields($this->event);
		foreach($eventFields as $field) {
			if(model_ContactField::isVisibleTo($field, array('id' => $regTypeId)) && model_ContactField::isRequired($field)) {
				$name = model_ContentType::$CONTACT_FIELD.'_'.$field['id'];
				$value = model_Registrant::getInformationValue($this->registration, $field);		
						
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
			return $html;
		}
	}
}

?>