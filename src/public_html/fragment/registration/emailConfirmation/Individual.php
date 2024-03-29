<?php

class fragment_registration_emailConfirmation_Individual extends template_Template
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
		return <<<_
			<br/>
			<br/>
			<div style="font-size:20px; font-weight:bold;">
				Registrant {$this->num}
			</div>
			<br/>
			
			<table style="border-collapse:separate; border-spacing:10px 5px;">
				{$this->getInformation($this->event, $this->registration)}
				{$this->getOptions($this->event, $this->registration)}
			</table>
_;
	}
	
	private function getInformation($event, $registration) {
		$date = substr($registration['dateRegistered'], 0, 10);
		
		$regType = db_RegTypeManager::getInstance()->find(array(
			'eventId' => $registration['eventId'],
			'id' => $registration['regTypeId']
		));
		
		$confirmationNumber = model_Registrant::getConfirmationNumber($registration);
		
		$dateCancelledRow = '';
		if(!empty($registration['dateCancelled'])) {
			$dateCancelled = substr($registration['dateCancelled'], 0, 10);
			$dateCancelledRow = <<<_
				<tr>
					<td style="font-weight:bold; vertical-align:top;">Date Cancelled</td>
					<td style="vertical-align:top;">{$dateCancelled}</td>
				</tr>
_;
		}
		
		return <<<_
				<tr>
					<td style="font-weight:bold; vertical-align:top;">Date Registered</td>
					<td style="vertical-align:top;">
						{$date}
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold; vertical-align:top;">Registration Type</td>
					<td style="vertical-align:top;">
						{$regType['description']}
					</td>
				</tr>
				<tr>
					<td style="font-weight:bold; vertical-align:top;">Confirmation Number</td>
					<td style="vertical-align:top;">
						{$confirmationNumber}
					</td>
				</tr>
				
				{$dateCancelledRow}
											
				{$this->getInformationRows($event, $registration)}
_;
	}
	
	private function getInformationRows($event, $registration) {
		$html = '';
		
		$pages = model_EventPage::getVisiblePages($event, array('id' => $registration['categoryId']));
		foreach($pages as $page) {
			$fragment = new fragment_registration_emailConfirmation_PageInformation($page, $registration);
			$html .= $fragment->html();
		}
		
		return $html;
	}
	
	private function getOptions($event, $registration) {
		$regOpts = new fragment_registration_emailConfirmation_RegOptions($event, $registration);
		$varOpts = new fragment_registration_emailConfirmation_VariableOptions($event, $registration);
		
		return <<<_
			<tr>
				<td colspan="2" style="font-size:20px;">
					<br/>
					Selected Options
				</td>
			</tr>
			
			{$regOpts->html()}
			
			{$varOpts->html()}
			
			{$this->getIndividualTotal($event, $registration)}
_;
		
	}
	
	private function getIndividualTotal($event, $registration) {
	
		$cost = db_reg_RegistrationManager::getInstance()->findTotalCost($registration);
		$costDisplay = '$'.number_format($cost, 2);
		
		return <<<_
			<tr>
				<td colspan="2">
					<div style="border-top: 2px solid black;"></div>
				</td>
			</tr>
			<tr>
				<td style="font-weight:bold; vertical-align:top;">Registrant Subtotal</td>
				<td style="font-weight:bold; vertical-align:top;">
					{$costDisplay}
				</td>
			</tr>
_;
	}
}

?>