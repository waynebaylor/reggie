<?php

class fragment_reg_summary_SummaryPage extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$allSummaries = '';
		
		$registrations = model_RegSession::getRegistrations();
		foreach($registrations as $index => $reg) {
			$allSummaries .= $this->getIndividualSummary($index);	
		}
		
		return <<<_
				{$allSummaries}
				
				<div style="background-color:#ccc; padding:5px; margin-bottom:10px; font-size:1.2em;">
					Payment
				</div>
			
				<table class="summary">
				{$this->getGrandTotal()}
				
				{$this->getPaymentInfo()}
				</table>
			
			<div class="section-divider"></div>

			<div class="cancellation-policy">
				{$this->event['cancellationPolicy']}
			</div>
			
			<div class="section-divider"></div>
_;
	}
	
	private function getIndividualSummary($index) {
		$rows = array();
		
		$regType = new fragment_reg_summary_RegType($this->event, $index);
		$rows[] = $regType->html();
		
		$information = new fragment_reg_summary_Information($this->event, $index);
		$rows[] = $information->html();
		
		$regOptions = new fragment_reg_summary_RegOptions($this->event, $index);
		$rows[] = $regOptions->html();
		
		$varQuantity = new fragment_reg_summary_VariableQuantity($this->event, $index);
		$rows[] = $varQuantity->html();
		
		// remove empty string values.
		$rows = array_filter($rows);
		
		$rows = join($this->getDivider(), $rows);
		
		// don't display a number if there is only one registrant.
		$num = count(model_RegSession::getRegistrations()) > 1? $index + 1 : '';
		
		return <<<_
			<div style="background-color:#ccc; padding:5px; margin-bottom:10px; font-size:1.2em;">
				Registrant {$num}
			</div>
			
			<table class="summary">
				{$rows}
				
				<tr><td colspan="2">
				<div class="summary-divider" style="border-top: 2px solid #ccc;"></div>
				</td></tr>
				
				{$this->getIndividualTotal($index)}
			</table>
			
			<div class="section-divider"></div>
_;
	}
	
	private function getDivider() {
		return <<<_
			<tr>
				<td colspan="2">
					<div class="summary-divider"></div>
				</td>
			</tr>
_;
	}
	
	private function getIndividualTotal($index) {
		$cost = model_Registration::getTotalPersonCost($this->event, $index);
		
		if($cost > 0) {
			return <<<_
				<tr>
					<td class="label">Individual Subtotal</td>
					<td class="details">
						<div class="price">\${$cost}</div>
					</td>
				</tr>
_;
		}
		
		return '';
	}
	
	private function getGrandTotal() {
		$cost = model_Registration::getTotalCost($this->event);
		
		return <<<_
			<tr>	
				<td class="label">Total Due</td>
				<td class="details">
					<div class="price">\${$cost}</div>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<div class="summary-divider"></div>
				</td>
			</tr>
_;
	}
	
	private function getPaymentInfo() {
		$paymentInfo = new fragment_reg_summary_PaymentInfo($this->event);
		return $paymentInfo->html();
	}
}

?>