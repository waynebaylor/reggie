<?php

class fragment_reg_ContactFields extends template_Template
{
	private $section;
	
	function __construct($section) {
		parent::__construct();

		$this->section = $section;
	}
	
	public function html() {
		$regType = model_reg_Session::getRegType();
		
		if($this->section['numbered'] === 'true') {
			return $this->orderedFields($regType, $this->section['content']);
		}
		else {
			return $this->unorderedFields($regType, $this->section['content']);
		}
	}
	
	private function orderedFields($regType, $fields) {
		$html = '';
		
		foreach($fields as $field) {
			if(model_ContactField::isVisibleTo($field, $regType)) {
				$required = model_ContactField::isRequired($field)? 'required' : '';
				$f = new fragment_reg_ContactField($field);
				
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
				$required = model_ContactField::isRequired($field)? 'required' : '';
				$f = new fragment_reg_ContactField($field);
				
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
}

?>