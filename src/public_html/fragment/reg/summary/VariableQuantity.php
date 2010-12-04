<?php

class fragment_reg_summary_VariableQuantity extends template_Template
{
	private $event;
	private $index;
	
	function __construct($event, $index) {
		parent::__construct();
		
		$this->event = $event;
		$this->index = $index;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(model_Section::containsVariableQuantityOptions($section)) {
					foreach($section['content'] as $option) {
						$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
						$quantity = model_RegSession::getVariableQuantityOption($name, $index);
						if(!empty($quantity) && $quantity > 0) {
							$price = model_RegOption::getPrice($this->event, $option);
							$priceDisplay = number_format($price['price'], 2);

							$total = number_format($price['price']*$quantity, 2);
							
							$html .= <<<_
								<tr>
									<td class="label">{$option['description']}</td>
									<td class="details">
										<div class="price">\${$total}</div>
										{$this->escapeHtml($quantity)} @ \${$priceDisplay}
									</td>
								</tr>
_;
						}
					}
				}
			}
		}
		
		if(empty($html)) {
			return '';
		}
		else {
			return $html;
		}
	}
}

?>