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
		$regTypeId = model_reg_Session::getRegType();
		$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);

		if(!empty($price)) {
			// check option capacity first.
			if($this->optionAtCapacity($option)) {
				$quantityInput = '';
				$price = 'Sold&nbsp;out.';
			}
			else {
				$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
				$value = model_reg_Session::getVariableQuantityOption($name);
					
				$quantityInput = <<<_
					{$this->HTML->text(array(
						'name' => $name,
						'value' => $value,
						'size' => 2
					))}
					&#64;&nbsp;			
_;

				$price = '$'.number_format($price['price'], 2);
			}

			return <<<_
				<tr>
					<td class="var-option-description">{$option['description']}</td>
					<td class="var-option-quantity">
						{$quantityInput}
					</td>
					<td class="var-option-price">
						{$price}
					</td>
				</tr>
_;
		}
		
		return '';
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