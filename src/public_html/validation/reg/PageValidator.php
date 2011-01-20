<?php

class validation_reg_PageValidator
{
	private $event;
	private $page;
	
	function __construct($event, $page) {
		$this->event = $event;
		$this->page = $page;
	}
	
	public function validate() {
		$errors = array();
		
		foreach($this->page['sections'] as $section) {
			$errors = array_merge($errors, $this->validateSection($section));
		}
		
		return $errors;
	}

	private function validateSection($section) {
		$errors = array();

		if(model_Section::containsRegTypes($section)) {
			$errors = array_merge($errors, $this->validateRegTypes($section));
		}
		else if(model_Section::containsContactFields($section)) {
			$errors = array_merge($errors, $this->validateContactFields($section));
		}
		else if(model_Section::containsRegOptions($section)) {
			$errors = array_merge($errors, $this->validateRegOptions($section));
		}
		
		return $errors;
	}
	
	private function validateRegTypes($section) {
		$errors = array();

		$name = model_ContentType::$REG_TYPE.'_regType';

		$regType = RequestUtil::getValue($name, NULL);
		if(empty($regType)) {
			$errors[$name] = 'Please choose a Registration Type.';
		}

		return $errors;
	}
	
	private function validateContactFields($section) {
		$errors = array();

		$regType = model_Event::getRegTypeById($this->event, model_reg_Session::getRegType());
			
		foreach($section['content'] as $field) {
			// only validate fields that are visible to the selected reg type.
			if(model_ContactField::isVisibleTo($field, $regType)) {
				$errors = array_merge($errors, $this->validateContactField($field));
			}
		}

		return $errors;
	}
	
	private function validateRegOptions($section) {
		$errors = array();
		
		$regType = model_Event::getRegTypeById($this->event, model_reg_Session::getRegType());
			
		foreach($section['content'] as $regOptionGroup) {
			$errors = array_merge($errors, $this->validateRegOptionGroup($regOptionGroup));
		}

		return $errors;
	}
	
	private function validateContactField($field) {
		$errors = array();
		
		$fieldName = model_ContentType::$CONTACT_FIELD.'_'.$field['id'];
		
		$rules = $field['validationRules'];
		foreach($rules as $rule) {
			// if there's already an error with a field, don't bother
			// checking further.
			if(empty($errors[$fieldName])) {
				$fieldValue = RequestUtil::getValue($fieldName, NULL);
			
				switch($rule['id']) {
					case model_Validation::$REQUIRED:
						if(!isset($fieldValue) || trim($fieldValue) === '') {
							if(in_array($field['formInput']['id'], array(model_FormInput::$CHECKBOX, model_FormInput::$RADIO))) {
								$errors[$fieldName] = 'Please choose an option.';
							}
							else {
								$errors[$fieldName] = "{$field['displayName']} is required.";
							}
						}
						break;
					case model_Validation::$MIN_LENGTH:
						// min length restriction is only checked if value is non-empty.
						if(strlen($fieldValue) > 0 && strlen($fieldValue) < $rule['value']) {
							$errors[$fieldName] = "{$field['displayName']} minimum length is {$rule['value']}.";
						}
						break;
					case model_Validation::$MAX_LENGTH:
						if(strlen($fieldValue) > $rule['value']) {
							$errors[$fieldName] = "{$field['displayName']} maximum length is {$rule['value']}.";
						}
						break;
				}
			}	
		}
		
		return $errors;
	}
	
	private function validateRegOptionGroup($group) {
		$errors = array();
		
		$name = model_ContentType::$REG_OPTION.'_'.$group['id'];
		
		if($group['required'] === 'true') {
			// there may be multiple values if this is a checkbox, but we only 
			// want to know if there is at least one value.
			$value = RequestUtil::getValue($name, NULL);
			if(empty($value)) {
				$inputName = ($group['multiple'] === 'true')? $name.'[]' : $name;
				$errors[$inputName] = 'Please choose an option.';
			}
		}
		
		if($group['multiple'] === 'true') {
			$values = RequestUtil::getValueAsArray($name, array());
			$min = intval($group['minimum'], 10);
			$max = intval($group['maximum'], 10);

			// only apply if min/max are greater than 0.
			if($min > 0 && $min === $max && count($values) !== $min) {
				$errors[$name.'[]'] = "Please choose {$min} option(s).";
			}
			else if($min > 0 && count($values) < $min) {
				$errors[$name.'[]'] = "Please choose at least {$min} option(s).";
			}
			else if($max > 0 && $max < count($values)) {
				$errors[$name.'[]'] = "You may choose up to {$max} option(s).";
			}
		}
		
		return $errors;
	}
}

?>