<?php

class fragment_groupSummary_VariableQuantity extends template_Template
{
	private $event;
	private $registration;
	
	function __construct($event, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(model_Section::containsVariableQuantityOptions($section)) {
					foreach($section['content'] as $option) {
						$quantity = model_Registrant::getVariableQuantityAmount($this->registration, $option['id']);
						if(!empty($quantity) && $quantity > 0) {
							$price = model_Registrant::getVariableQuantityPriceId($this->registration, $option['id']);
							$priceDisplay = number_format($price['price'], 2);

							$total = number_format($price['price']*$quantity, 2);
							
							$html .= <<<_
								<tr>
									<td>{$option['description']}</td>
									<td class="details" style="white-space:nowrap;">
										\${$total} ({$this->escapeHtml($quantity)} @ \${$priceDisplay})
									</td>
									<td></td>
								</tr>
_;
						}
					}
				}
			}
		}

		return $html;
	}
}

?>