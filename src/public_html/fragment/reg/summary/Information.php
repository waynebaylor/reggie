<?php

class fragment_reg_summary_Information extends template_Template
{
	private $event;
	private $index;
	
	function __construct($event, $index) {
		parent::__construct();
		
		$this->event = $event;
		$this->index = $index;
	}
	
	public function html() {
		$html = '';
		
		$regType = model_reg_Session::getRegType($this->index);
		
		$eventFields = model_Event::getInformationFields($this->event);
		foreach($eventFields as $field) {
			if(model_ContactField::isVisibleTo($field, $regType)) {
				$name = model_ContentType::$CONTACT_FIELD.'_'.$field['id'];
				$value = model_reg_Session::getContactField($name, $this->index);			
						
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
					else if(!empty($value)) {
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