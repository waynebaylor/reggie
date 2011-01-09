<?php

class fragment_validation_ValidationErrors extends template_Template
{
	private $errors;
	
	function __construct($errors) {
		parent::__construct();
		
		$this->errors = $errors;
	}
	
	public function html() {  
		return $this->HTML->textarea(array(
			'id' => 'xhr-response',
			'class' => 'hide',
			'name' => 'validationError',
			'value' => $this->getErrors()
		));
	}
	
	private function getErrors() {
		return <<<_
			{
				{$this->getErrorFields()}
			}
_;
	}
	
	private function getErrorFields() {
		$properties = array();
		
		if(!empty($this->errors['general'])) {
			$properties[] = '"general":'.json_encode($this->errors['general']);
			
			unset($this->errors['general']);
		}
		
		foreach($this->errors as $name => $text) {
			$encodedText = json_encode($text);	
			$properties[] = "'{$name}': {$encodedText}";
		}
		
		return implode(',', $properties);
	}
}

?>