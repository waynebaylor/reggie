<?php

class fragment_reg_RegType extends template_Template
{
	private $regType;
	
	function __construct($regType) {
		parent::__construct();
		
		$this->regType = $regType; 
	}
	
	public function html() {
		if($this->isVisible()) {
			$checked = model_reg_Session::getRegType() === $this->regType['id'];
			
			return <<<_
				<div class="reg-type-option">
					{$this->HTML->radio(array(
						'label' => $this->regType['description'],
						'name' => model_ContentType::$REG_TYPE."_regType",
						'value' => $this->regType['id'],
						'checked' => $checked
					))}
				</div>
_;
		}
		else {
			return '';
		}
	}
	
	private function isVisible() {
		$category = model_reg_Session::getCategory();
		return model_RegType::isVisibleTo($this->regType, $category);
	}
}

?>