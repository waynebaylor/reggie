<?php

class fragment_registration_summary_Individual extends template_Template
{
	private $event;
	private $registration;
	private $num;
	
	function __construct($event, $registration, $num, $isPdfFormat = FALSE) {
		parent::__construct();
		
		$this->event = $event;
		$this->registration = $registration;
		$this->num = $num;
		$this->isPdfFormat = $isPdfFormat;
	}
	
	public function html() {
		$regType = db_RegTypeManager::getInstance()->find(array(
			'eventId' => $this->event['id'],
			'id' => $this->registration['regTypeId']
		));
		
		if($this->isPdfFormat) {
			$heading = <<<_
				<table>
					<tr><td></td></tr>
					<tr><td style="font-weight:bold; background-color:#ccc;">
						<br><br>
						&nbsp;Registrant {$this->num}
						<br>
					</td></tr>
					<tr><td></td></tr>
				</table>
_;
		}
		else {
			$heading = <<<_
				<div class="sub-divider"></div>
			
				<div class="registrant-heading">
					Registrant {$this->num}
				</div>
_;
		}
		
		return <<<_
			{$heading}		
			
			<table class="summary">
				<tr>
					<td>
						{$this->getInformation($this->event, $this->registration)}
					</td>
				</tr>
				<tr>
					<td>
						{$this->getOptions($this->event, $this->registration)}
					</td>
				</tr>
			</table>
_;
	}
	
	private function getOptions($event, $registration) {
		$regOpts = new fragment_registration_summary_RegOptions($event, $registration);
		$varOpts = new fragment_registration_summary_VariableOptions($event, $registration);
		
		$extraSpace = ($this->isPdfFormat)? '<br><br>' : '';
		
		return <<<_
			{$extraSpace}
			
			<table>
				{$regOpts->html()}
				
				{$varOpts->html()}
				
				{$this->getIndividualTotal($event, $registration)}
			</table>
_;
		
	}
	
	private function getInformation($event, $registration) {
		$date = substr($registration['dateRegistered'], 0, 10);
		
		$regType = db_RegTypeManager::getInstance()->find(array(
			'eventId' => $this->event['id'],
			'id' => $registration['regTypeId']
		));
		
		$confirmationNumber = model_Registrant::getConfirmationNumber($registration);
		
		$dateCancelledRow = '';
			if(!empty($registration['dateCancelled'])) {
				$dateCancelled = substr($registration['dateCancelled'], 0, 10);
				$dateCancelledRow = <<<_
					<tr>
						<td class="label">Date Cancelled</td>
						<td>{$dateCancelled}</td>
					</tr>
_;
		}
		
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
				<tr>
					<td class="label">Confirmation Number</td>
					<td>
						{$confirmationNumber}
					</td>
				</tr>
				
				{$dateCancelledRow}
				
				{$this->getInformationRows($event, $registration)}
			</table>	
_;
	}
	
	private function getInformationRows($event, $registration) {
		$html = '';
		
		$pages = model_EventPage::getVisiblePages($event, array('id' => $registration['categoryId']));
		foreach($pages as $page) {
			$fragment = new fragment_registration_summary_PageInformation($page, $registration);
			
			$extraPadding = ($this->isPdfFormat)? '<br>' : '';
			
			$html .= $extraPadding;
			$html .= $fragment->html();
		}
		
		return $html;
	}
	
	private function getIndividualTotal($event, $registration) {
	
		$cost = db_reg_RegistrationManager::getInstance()->findTotalCost($registration);
		$costDisplay = '$'.number_format($cost, 2);
		
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
}

?>