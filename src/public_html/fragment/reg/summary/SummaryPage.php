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
		
		$registrations = model_reg_Session::getRegistrations();
		foreach($registrations as $index => $reg) {
			$registrant = new fragment_reg_summary_Registrant($this->event, $index);
			$allSummaries .= $registrant->html();	
		}
		
		return <<<_
				{$allSummaries}
				
				<div class="registrant-heading">
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
	
	private function getGrandTotal() {
		$cost = model_reg_Registration::getTotalCost($this->event);
		$costDisplay = number_format($cost, 2);
		
		return <<<_
			<tr>	
				<td class="label">Total Due</td>
				<td class="details">
					<div class="price">\${$costDisplay}</div>
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