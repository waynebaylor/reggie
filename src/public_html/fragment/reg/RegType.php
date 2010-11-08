<?php

require_once 'HTML.php';
require_once 'template/Template.php';
require_once 'model/RegType.php';
require_once 'model/RegSession.php';
require_once 'model/ContentType.php';

class fragment_reg_RegType extends template_Template
{
	private $regType;
	
	function __construct($regType) {
		parent::__construct();
		
		$this->regType = $regType; 
	}
	
	public function html() {
		if($this->isVisible()) {
			$checked = model_RegSession::getRegType() === $this->regType['id'];
			
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
		$category = model_RegSession::getCategory();
		return model_RegType::isVisibleTo($this->regType, $category);
	}
}

?>