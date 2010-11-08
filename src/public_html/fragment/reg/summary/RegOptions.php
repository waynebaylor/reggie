<?php

class fragment_reg_summary_RegOptions extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			<tr>
				<td class="label">Registration Options</td>
				<td class="details">
					{$this->getRegOptions()}
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="summary-divider"></div>
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
		
		return <<<_
			<ul class="reg-option-group">{$html}</ul>
_;
	}
	
	private function getRegOption($option) {
		$html = '';
		
		if(model_Registration::isRegOptionSelected($option)) {
			$regType = model_RegSession::getRegType();
			$price = model_RegOption::getPrice($regType, $option);
			$priceDisplayed = ($option['showPrice'] === 'true')? '$'.number_format($price['price'], 2) : '';
			$html .= <<<_
				<li>
					<div class="reg-option">
						<div class="price">{$priceDisplayed}</div>
						{$option['description']}
_;

			foreach($option['groups'] as $group) {
				$html .= $this->getRegOptionGroup($group);
			}
			
			$html .= '</div></li>';
		}
		
		return $html;
	}
}

?>