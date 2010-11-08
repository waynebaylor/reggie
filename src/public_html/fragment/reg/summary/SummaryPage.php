<?php

class fragment_reg_summary_SummaryPage extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$regType = new fragment_reg_summary_RegType($this->event);
		$information = new fragment_reg_summary_Information($this->event);
		$regOptions = new fragment_reg_summary_RegOptions($this->event);
		$varQuantity = new fragment_reg_summary_VariableQuantity($this->event);
		$paymentInfo = new fragment_reg_summary_PaymentInfo($this->event);
		
		return <<<_
			<table class="summary">
				{$regType->html()}
				
				{$information->html()}
				
				{$regOptions->html()}
				
				{$varQuantity->html()}
				
				{$this->getTotalDueRow()}
				
				{$paymentInfo->html()}
			</table>	
			
			<div class="section-divider"></div>

			<div class="cancellation-policy">
				{$this->event['cancellationPolicy']}
			</div>
			
			<div class="section-divider"></div>
_;
	}
	
	private function getTotalDueRow() {
		$cost = model_Registration::getTotalCost($this->event);
		
		if($cost > 0) {
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
		
		return '';
	}
}

?>