<?php

class fragment_reg_regOption_VariableQuantityOptions extends template_Template
{
	private $options;
	
	function __construct($options) {
		parent::__construct();
	
		$this->options = $options;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->options as $option) {
			$html .= $this->getRow($option);	
		}
		
		return <<<_
			<table class="reg-options">
				{$html}
			</table>
_;
	}
	
	private function getRow($option) {
		$price = $this->getPrice($option);

		if(!empty($price)) {
			return <<<_
				<tr>
					<td>{$option['description']}</td>
					<td class="price">
						{$price}
					</td>
				</tr>
_;
		}
	}
	
	private function getPrice($option) {
		if($option['showPrice'] !== 'true') {
			return '&nbsp;';
		}
		
		if($this->optionAtCapacity($option)) {
			return 'Sold out.';
		}
		else {
			$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
			$value = model_reg_Session::getVariableQuantityOption($name);
			
			$regType = model_reg_Session::getRegType();
			$price = model_RegOption::getPrice($regType, $option);
			
			//display like: @ $45.95
			$priceDisplay = ' &#64; $'.number_format($price['price'], 2);
			
			return <<<_
				<input type="text" name="{$name}" value="{$value}" size="2"/>{$priceDisplay}		
_;
		}
	}
	
	private function optionAtCapacity($option) {
		if(is_numeric($option['capacity']) && $option['capacity'] > 0) {
			$currentCount = db_reg_RegistrationManager::getInstance()->findVariableOptionCount($option);
			return $currentCount >= $option['capacity'];
		}
		
		return false;
	}
}

?>