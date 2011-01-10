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
		
		$regTypeId = $this->registration['regTypeId'];
		$selectedOptions = model_Registrant::getRegOptionIds($this->registration);
		if(in_array($option['id'], $selectedOptions)) {
			$priceId = model_Registrant::getPriceId($this->registration, $option['id']);
			$price = db_RegOptionPriceManager::getInstance()->find($priceId);
			$priceDisplayed = ($option['showPrice'] === 'true')? '$'.number_format($price['price'], 2) : '';
			$cancelled = model_Registrant::isOptionCancelled($this->registration, $option['id'])? 'Cancelled' : '';
			
			$html .= <<<_
				<tr>
					<td>{$option['description']}</td>
					<td>{$priceDisplayed}</td>
					<td>{$cancelled}</td>
				</tr>
_;

			foreach($option['groups'] as $group) {
				$html .= $this->getRegOptionGroup($group);
			}
		}
		
		return $html;
	}
}

?>