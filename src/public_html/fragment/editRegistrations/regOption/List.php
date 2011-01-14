<?php

class fragment_editRegistrations_regOption_List extends template_Template
{
	private $event;
	private $report;
	private $regGroup;
	private $registration;
	
	function __construct($event, $report, $regGroup, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->regGroup = $regGroup;
		$this->registration = $registration;
	}
	
	public function html() {
		return <<<_
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Description</th>
						<th>Price</th>
						<th>Date Added</th>
						<th>Date Cancelled</th>
						<th>Options</th>			
					</tr>
					{$this->getOptions()}
				</table>
			</div>
_;
	}
	
	private function getOptions() {
		$html = '';
		
		$regOptGroups = model_Event::getRegOptionGroups($this->event);
		
		foreach($regOptGroups as $group) {
			$html .= $this->getRegOptionGroupHtml($group, $this->registration);
		}
		
		return $html;
	}
	
	private function getRegOptionGroupHtml($group, $registration) {
		$html = '';
		
		foreach($group['options'] as $opt) {
			$html .= $this->getRegOptionRow($registration, $opt);
			
			foreach($opt['groups'] as $optGroup) {
				$html .= $this->getRegOptionGroupHtml($optGroup, $registration);
			}	
		}
		
		return $html;
	}

	/**
	 * if the registration selected the given option AND the selection has not been cancelled, then a row is returned.
	 */
	private function getRegOptionRow($registration, $regOption) {
		$html = '';
		
		// the registrant may have the same option multiple times if they have cancelled and re-registered.
		foreach($registration['regOptions'] as $o) {
			if($o['regOptionId'] == $regOption['id']) {
				$price = db_RegOptionPriceManager::getInstance()->find($o['priceId']);
				$priceDisplay = '$'.number_format($price['price'], 2);
				$dateAdded = date_format(date_create($o['dateAdded']), 'Y-m-d');
				
				if(empty($o['dateCancelled'])) {
					$cancelClass = '';
					$dateCancelled = '';
					$cancelLink = $this->HTML->link(array(
						'label' => 'Cancel',
						'href' => '/admin/registration/RegOption',
						'parameters' => array(
							'a' => 'cancelRegOption',
							'id' => $o['id'],
							'groupId' => $this->regGroup['id'],
							'reportId' => $this->report['id']
						)
					));
				}
				else {
					$cancelClass = 'reg-option-cancelled';
					$dateCancelled = date_format(date_create($o['dateCancelled']), 'Y-m-d');	
					$cancelLink = '';
				}
				
				$html .= <<<_
					<tr class="{$cancelClass}">
						<td style="vertical-align:top;">{$regOption['description']}</td>
						<td style="vertical-align:top;">{$priceDisplay}</td>
						<td style="vertical-align:top;">{$dateAdded}</td>
						<td style="vertical-align:top;">{$dateCancelled}</td>
						<td style="vertical-align:top;">{$cancelLink}</td>
					</tr>
_;
			}
		}
		
		return $html;
	}
}

?>