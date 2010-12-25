<?php

class fragment_reg_ContactFields extends template_Template
{
	private $section;
	private $regTypeId;
	private $fieldValues; // field id -> value
	
	function __construct($section, $regTypeId, $fieldValues) {
		parent::__construct();

		$this->section = $section;
		$this->regTypeId = $regTypeId;
		$this->fieldValues = $fieldValues;
	}
	
	public function html() {
		if($this->section['numbered'] === 'true') {
			return $this->orderedFields($this->regTypeId, $this->section['content']);
		}
		else {
			return $this->unorderedFields($this->regTypeId, $this->section['content']);
		}
	}
	
	private function orderedFields($regType, $fields) {
		$html = '';
		
		foreach($fields as $field) {
			if(model_ContactField::isVisibleTo($field, $regType)) {
				$value = $this->getFieldValue($field);
				$f = new fragment_reg_ContactField($field, $value);
				
				$required = model_ContactField::isRequired($field)? 'required' : '';
				
				$html .= <<<_
					<li>
						<div>
							<span class="{$required}">{$field['displayName']}</span>
						</div>
						<div>
							{$f->html()}
						</div>
					</li>			
_;
			}
		}
		
		return <<<_
			<ol class="contact-fields">{$html}</ol>
_;
	}
	
	private function unorderedFields($regType, $fields) {
		$html = '';

		foreach($fields as $field) {
			if(model_ContactField::isVisibleTo($field, $regType)) {
				$value = $this->getFieldValue($field);
				$f = new fragment_reg_ContactField($field, $value);
				
				$required = model_ContactField::isRequired($field)? 'required' : '';
				
				$html .= <<<_
					<tr>
						<td class="contact-field-label">
							<span class="{$required}">{$field['displayName']}</span>
						</td>
						<td>
							{$f->html()}
						</td>
					</tr>
_;
			}
		}

		return <<<_
			<table class="contact-fields">{$html}</table>
_;
	}
	
	private function getFieldValue($field) {
		$id = $field['id'];
		return isset($this->fieldValues[$id])? $this->fieldValues[$id] : '';
	}
}

?>