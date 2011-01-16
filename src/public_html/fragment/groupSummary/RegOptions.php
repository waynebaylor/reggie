<?php

class fragment_groupSummary_RegOptions extends template_Template
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
				<td class="label">Selected Options</td>
				<td class="details">
					{$this->getRegOptions()}
				</td>
			</tr>
_;
	}
	
	private function getRegOptions() {
		$html = '';
		
		$eventOptionGroups = model_Event::getRegOptionGroups($this->event);
		foreach($eventOptionGroups as $group) {
			$html .= $this->getRegOptionGroup($group);
		}
		
		return $html;
	}
	
	private function getRegOptionGroup($group) {
		$html = '';
		
		foreach($group['options'] as $option) {
			$html .= $this->getRegOption($option);
		}
		
		return $html;
	}
	
	private function getRegOption($option) {
		$html = '';
		
		foreach($this->registration['regOptions'] as $regOption) {
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