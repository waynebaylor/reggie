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
			$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
			$value = model_RegSession::getVariableQuantityOption($name);
			
			$priceDisplay = ' &#64; $'.number_format($price['price'], 2);
			
			return <<<_
				<tr>
					<td>{$option['description']}</td>
					<td class="price">
						<input type="text" name="{$name}" value="{$value}" size="2"/>{$priceDisplay}
					</td>
				</tr>
_;
		}
	}
	
	private function getPrice($option) {
		$regType = model_RegSession::getRegType();
		
		return model_RegOption::getPrice($regType, $option);
	}
}

?>