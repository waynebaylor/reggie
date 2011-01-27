<?php

class fragment_reg_ContactField extends template_Template
{
	private $field;
	private $value;
	
	function __construct($field, $value) {
		parent::__construct();

		$this->field = $field;
		$this->value = $value;
	}
	
	public function html() {
		$formInput = $this->field['formInput'];
		
		switch($formInput['id']) {
			case model_FormInput::$TEXT:
				return $this->textHtml();
				
			case model_FormInput::$TEXTAREA:
				return $this->textareaHtml();
				
			case model_FormInput::$CHECKBOX:
				return $this->checkboxHtml();
				
			case model_FormInput::$RADIO:
				return $this->radioHtml();
				
			case model_FormInput::$SELECT:
				return $this->selectHtml();		
		}
	}
	
	private function textHtml() {
		$config = $this->getBaseConfig();
		
		$attrs = $this->getAttributeArray();
		$config = array_merge($config, $attrs);
		
		$validationRules = $this->getValidationArray();
		$config = array_merge($config, $validationRules);
		
		return $this->HTML->text($config);
	}

	private function textareaHtml() {
		$config = $this->getBaseConfig();
		
		$attrs = $this->getAttributeArray();
		$config = array_merge($config, $attrs);
		
		$validationRules = $this->getValidationArray();
		$config = array_merge($config, $validationRules);
		
		return $this->HTML->textarea($config);
	}

	private function checkboxHtml() {
		$config = $this->getBaseConfig();
		
		$opts = $this->getOptionsConfig();
		$config = array_merge($config, $opts);
		
		return $this->HTML->checkboxes($config);
	}

	private function radioHtml() {
		$config = $this->getBaseConfig();
		
		// radio buttons should start with one option selected.
		if(empty($config['value'])) {
			$o = $this->getSelectedOption();
			$config['value'] = $o['id'];
		}
		
		$opts = $this->getOptionsConfig();
		$config = array_merge($config, $opts);
		
		return $this->HTML->radios($config);
	}

	private function selectHtml() {
		$config = $this->getBaseConfig();
		
		$opts = $this->getOptionsConfig();
		$config = array_merge($config, $opts);
		
		return $this->HTML->select($config);
	}
	
	private function getBaseConfig() {
		$config = array(
			'name' => model_ContentType::$CONTACT_FIELD.'_'.$this->field['id'],
			'value' => isset($this->value)? $this->value : ''
		);

		return $config;
	}
	
	private function getOptionsConfig() {
		$opts = array(
			'items' => array()
		);
		
		$validationRules = $this->getValidationArray();
		
		foreach($this->field['options'] as $option) {
			$opts['items'][] = array(
				'label' => $option['displayName'],
				'value' => $option['id']
			);
		}
		
		return $opts;
	}
	
	private function getAttributeArray() {
		$attrs = array();
		
		foreach($this->field['attributes'] as $a) {
			if(!empty($a['value'])) {
				$attrs[$a['name']] = $a['value'];
			}
		}
		
		return $attrs;
	}
	
	private function getValidationArray() {
		$v = array();

		foreach($this->field['validationRules'] as $rule) {
			if(!empty($rule['value'])) {
				// so far, this is the only validation constraint that can be
				// implemented in HTML.
				if(intval($rule['id'], 10) === model_Validation::$MAX_LENGTH) {
					$v[$rule['name']] = $rule['value'];
				}
			}
		}

		return $v;
	}
	
	/**
	 * Returns the option that should be selected by default. if none is
	 * set as the default, then the first option is returned. otherwise
	 * an empty string is returned.
	 */
	private function getSelectedOption() {
		if(!empty($this->field['options'])) {
			foreach($this->field['options'] as $option) {
				if($option['defaultSelected'] === 'true') {
					return $option;
				}
			}
		}
		
		return array('id' => '');
	}
}

?>