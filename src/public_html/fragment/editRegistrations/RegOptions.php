<?php

class fragment_editRegistrations_RegOptions extends template_Template
{
	private $section;
	private $regGroup;
	private $registration;
	
	function __construct($section, $regGroup, $registration) {
		parent::__construct();
		
		$this->section = $section;
		$this->regGroup = $regGroup;
		$this->registration = $registration;
	}
	
	public function html() {
		$rows = array(
			'selected' => '',
			'cancelled' => ''
		);
		
		foreach($this->section['content'] as $group) {
			$r = $this->getRegOptionGroupHtml($group, $this->registration);
			$rows['selected'] .= $r['selected'];
			$rows['cancelled'] .= $r['cancelled'];
		}
		
		return <<<_
			<table style="border-collapse:separate; border-spacing:20px 10px;">
				{$rows['selected']}
			</table>
			
			<div class="divider"></div>
			
			<table style="color:#555; border-collapse:separate; border-spacing:20px 10px;">
				{$rows['cancelled']}
			</table>
_;
	}
	
	private function getRegOptionGroupHtml($group, $registration) {
		$rows = array(
			'selected' => '',
			'cancelled' => ''
		);
		
		foreach($group['options'] as $opt) {
			$r = $this->getRegOptionRow($registration, $opt);
			$rows['selected'] .= $r['selected'];
			$rows['cancelled'] .= $r['cancelled'];
			
			foreach($opt['groups'] as $optGroup) {
				$r = $this->getRegOptionGroupHtml($optGroup, $registration);
				$rows['selected'] .= $r['selected'];
				$rows['cancelled'] .= $r['cancelled'];
			}	
		}
		
		return $rows;
	}

	/**
	 * if the registration selected the given option AND the selection has not been cancelled, then a row is returned.
	 */
	private function getRegOptionRow($registration, $regOption) {
		$rows = array(
			'selected' => '',
			'cancelled' => ''
		);
		
		foreach($registration['regOptions'] as $o) {
			if($o['regOptionId'] == $regOption['id']) {
				$price = db_RegOptionPriceManager::getInstance()->find($o['priceId']);
				$priceDisplay = '$'.number_format($price['price'], 2);
				
				if(empty($o['dateCancelled'])) {
					$rows['selected'] .= <<<_
						<tr>
							<td style="vertical-align:top;">{$regOption['description']}</td>
							<td style="text-align:right; vertical-align:top;">{$priceDisplay}</td>
							<td style="vertical-align:top;">
								{$this->HTML->link(array(
									'label' => 'Cancel',
									'href' => '/admin/registration/Registration',
									'parameters' => array(
										'a' => 'cancelRegOption',
										'id' => $o['id'],
										'groupId' => $this->regGroup['id']
									)
								))}
							</td>
						</tr>
_;
				}
				else {
					$dateCancelled = date_format(date_create($o['dateCancelled']), 'Y-m-d');
					$rows['cancelled'] .= <<<_
						<tr>
							<td style="vertical-align:top;">{$regOption['description']}</td>
							<td style="text-align:right; vertical-align:top;">{$priceDisplay}</td>
							<td style="vertical-align:top;">
								Cancelled {$dateCancelled}
							</td>
							<td style="vertical-align:top;">{$this->escapeHtml($o['comments'])}</td>
						</tr>
_;
				}
			}
		}
		
		return $rows;
	}
}

?>