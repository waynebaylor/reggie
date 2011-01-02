<?php

class fragment_reg_summary_Registrant extends template_Template
{
	private $event;
	private $index;
	
	function __construct($event, $index) {
		parent::__construct();
		
		$this->event = $event;
		$this->index = $index;
	}
	
	public function html() {
		return $this->getIndividualSummary($this->index);
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
		$num = count(model_reg_Session::getRegistrations()) > 1? $index + 1 : '';
		
		return <<<_
			<div class="registrant-heading">
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
		$cost = model_reg_Registration::getTotalPersonCost($this->event, $index);
		
		$costDisplay = number_format($cost, 2);
		
		if($cost > 0) {
			return <<<_
				<tr>
					<td class="label">Registrant Subtotal</td>
					<td class="details">
						<div class="price">\${$costDisplay}</div>
					</td>
				</tr>
_;
		}
		
		return '';
	}
}

?>