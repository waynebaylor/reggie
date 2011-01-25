<?php

class fragment_registration_summary_Individual extends template_Template
{
	private $event;
	private $registration;
	private $num;
	
	function __construct($event, $registration, $num) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
		$this->num = $num;
	}
	
	public function html() {
		$regType = db_RegTypeManager::getInstance()->find($this->registration['regTypeId']);
		
		return <<<_
			<div class="sub-divider"></div>
			
			<div class="registrant-heading">
				Registrant {$this->num}
			</div>
			
			<table class="summary">
				<tr>
					<td style="width:50%;">
						{$this->getInformation($this->event, $this->registration)}
					</td>
					<td style="width:50%;">
						{$this->getOptions($this->event, $this->registration)}
					</td>
				</tr>
			</table>
_;
	}
	
	private function getOptions($event, $registration) {
		$regOpts = new fragment_registration_summary_RegOptions($event, $registration);
		$varOpts = new fragment_registration_summary_VariableOptions($event, $registration);
		
		return <<<_
			<table>
				{$regOpts->html()}
				
				{$varOpts->html()}
				
				{$this->getIndividualTotal($event, $registration)}
			</table>
_;
		
	}
	
	private function getInformation($event, $registration) {
		$date = substr($registration['dateRegistered'], 0, 10);
		
		$regType = db_RegTypeManager::getInstance()->find($registration['regTypeId']);
		
		return <<<_
			<table>
				<tr>
					<td class="label">Date Registered</td>
					<td>
						{$date}
					</td>
				</tr>
				<tr>
					<td class="label">Registration Type</td>
					<td>
						{$regType['description']}
					</td>
				</tr>
				
				{$this->getInformationRows($event, $registration)}
			</table>	
_;
	}
	
	private function getInformationRows($event, $registration) {
		$html = '';
		
		$pages = model_EventPage::getVisiblePages($event, array('id' => $registration['categoryId']));
		foreach($pages as $page) {
			$fragment = new fragment_registration_summary_PageInformation($page, $registration);
			$html .= $fragment->html();
		}
		
		return $html;
	}
	
	private function getIndividualTotal($event, $registration) {
	
		$cost = db_reg_RegistrationManager::getInstance()->findTotalCost($registration);
		$costDisplay = '$'.number_format($cost, 2);
		
		if(!empty($event['paymentTypes'])) {
			return <<<_
				<tr>
					<td colspan="3">
						<div class="summary-divider" style="border-top: 2px solid #ccc;"></div>
					</td>
				</tr>
				<tr>
					<td class="label">Registrant Subtotal</td>
					<td class="details" style="font-weight:bold;">
						{$costDisplay}
					</td>
					<td></td>
				</tr>
_;
		}
		
		return '';
	}
}

?>