<?php

class fragment_registration_summary_VariableOptions extends template_Template
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
						$html .= $this->getOptionRow($this->registration, $option);
					}
				}
			}
		}
		
		return $html;
	}
	
	private function getOptionRow($registration, $option) {
		$quantity = model_Registrant::getVariableQuantityAmount($registration, $option['id']);
		
		if(!empty($quantity) && $quantity > 0) {
			$priceId = model_Registrant::getVariableQuantityPriceId($registration, $option['id']);
			$price = db_RegOptionPriceManager::getInstance()->find($priceId);
			$priceDisplay = '$'.number_format($price['price'], 2);

			$total = '$'.number_format($price['price']*$quantity, 2);
			
			return <<<_
				<tr>
					<td>{$option['description']}</td>
					<td class="details" style="white-space:nowrap;">
						{$total} ({$this->escapeHtml($quantity)} @ {$priceDisplay})
					</td>
				</tr>
_;
		}
		
		return '';
	}
}

?>