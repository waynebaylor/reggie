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
				<td colspan="3">
					<h3>Selected Options</h3>
				</td>
			</tr>
			{$this->getRegOptions($this->event, $this->registration)}
_;
	}
	
	private function getRegOptions($event, $registration) {
		$html = '';
		
		$eventOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($eventOptionGroups as $group) {
			$html .= $this->getRegOptionGroup($registration, $group);
		}

		if(empty($html)) {
			$html = '<tr><td>No options selected.</td></tr>';	
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
				$price = db_RegOptionPriceManager::getInstance()->find(array(
					'eventId' => $registration['eventId'],
					'id' => $regOption['priceId']
				));
				
				$priceDisplayed = ($option['showPrice'] === 'T')? '$'.number_format($price['price'], 2) : '';
				$cancelled = empty($regOption['dateCancelled'])? '' : '( Cancelled on '.substr($regOption['dateCancelled'], 0, 10).' )';
				
				$html .= <<<_
					<tr>
						<td>{$option['description']}</td>
						<td>{$priceDisplayed}</td>
						<td style="white-space:nowrap;">{$cancelled}</td>
					</tr>
_;
			}
		}

		foreach($option['groups'] as $group) {
			$html .= $this->getRegOptionGroup($registration, $group);
		}
		
		return $html;
	}
}

?>