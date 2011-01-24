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
		$regType = model_reg_Session::getRegType();
		$price = model_RegOption::getPrice($regType, $option);

		if(!empty($price)) {
			// check option capacity first.
			if($this->optionAtCapacity($option)) {
				$price = 'Sold out.';
			}
			else {
				$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
				$value = model_reg_Session::getVariableQuantityOption($name);
					
				$price = '$'.number_format($price['price'], 2);
			}

			return <<<_
				<tr>
					<td class="var-option-description">{$option['description']}</td>
					<td class="var-option-quantity">
						{$this->HTML->text(array(
							'name' => $name,
							'value' => $value,
							'size' => 2
						))}
						&#64;&nbsp;
					</td>
					<td class="var-option-price">
						{$price}
					</td>
				</tr>
_;
		}
		
		return '';
	}
	
	private function getPrice($option) {
		$regType = model_reg_Session::getRegType();
		$price = model_RegOption::getPrice($regType, $option);

		if(!empty($price)) {
			// check option capacity first.
			if($this->optionAtCapacity($option)) {
				return 'Sold out.';
			}
			else {
				$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
				$value = model_reg_Session::getVariableQuantityOption($name);
					
				//display like: @ $45.95
				$priceDisplay = ' &#64; $'.number_format($price['price'], 2);
					
				return <<<_
					<input type="text" name="{$name}" value="{$value}" size="2"/>{$priceDisplay}		
_;
			}
		}
		
		return null;
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