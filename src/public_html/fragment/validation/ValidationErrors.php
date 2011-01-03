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
		$jsonFragment = '';
		
		if(!empty($this->errors['general'])) {
			$generalMessages = '';
			foreach($this->errors['general'] as $msg) {
				$generalMessages .= "'{$msg}',";
			}
			
			rtrim($generalMessages, ',');
			
			$jsonFragment .= <<<_
				'general': [{$generalMessages}],			
_;
			unset($this->errors['general']);
		}
		
		foreach($this->errors as $name => $text) {
			// single quotes for json since it's used as the 
			// value of a hidden input and double quotes would
			// interfere with the input value quotes.
			$jsonFragment .= <<<_
				'{$name}': '{$text}',
_;
		}
		
		return rtrim($jsonFragment, ',');
	}
}

?>