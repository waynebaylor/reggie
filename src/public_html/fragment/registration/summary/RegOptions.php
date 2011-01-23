<?php

class fragment_registration_summary_RegOptions extends template_Template
{
	private $event;
	private $registration;
	
	function __construct($event, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
	}
	
	public function html() {
		return <<<_
			<tr>
				<td class="label" colspan="3">Selected Options</td>
			</tr>
			<tr>
				{$this->getRegOptions($this->event, $this->registration)}
			</tr>
_;
	}
	
	private function getRegOptions($event, $registration) {
		$html = '';
		
		$eventOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($eventOptionGroups as $group) {
			$html .= $this->getRegOptionGroup($registration, $group);
		}
		
		return $html;
	}
	
	private function getRegOptionGroup($registration, $group) {
		$html = '';
		
		foreach($group['options'] as $option) {
			$html .= $this->getRegOption($registration, $option);
		}
		
		return $html;
	}
	
	private function getRegOption($registration, $option) {
		$html = '';

		foreach($registration['regOptions'] as $regOption) {
			if($option['id'] == $regOption['regOptionId']) {
				$price = db_RegOptionPriceManager::getInstance()->find($regOption['priceId']);
				$priceDisplayed = ($option['showPrice'] === 'true')? '$'.number_format($price['price'], 2) : '';
				$cancelled = empty($regOption['dateCancelled'])? '' : 'Cancelled';
				
				$html .= <<<_
					<tr>
						<td>{$option['description']}</td>
						<td>{$priceDisplayed}</td>
						<td>{$cancelled}</td>
					</tr>
_;
			}
		}

		foreach($option['groups'] as $group) {
			$html .= $this->getRegOptionGroup($group);
		}
		
		return $html;
	}
}

?>