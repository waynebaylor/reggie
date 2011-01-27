<?php

class fragment_editRegistrations_payment_Payments extends template_Template
{
	private $event;
	private $report;
	private $group;
	
	function __construct($event, $report, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->group = $group;
	}
	
	public function html() {
		$list = new fragment_editRegistrations_payment_List($this->event, $this->report, $this->group);
		$add = new fragment_editRegistrations_payment_Add($this->event, $this->report, $this->group);
		
		$cost = db_reg_GroupManager::getInstance()->findTotalCost($this->group['id']);
		$paid = db_reg_GroupManager::getInstance()->findTotalPaid($this->group['id']);
		$remainingBalance = $cost - $paid;
		
		$cost = '$'.number_format($cost, 2);
		$paid = '$'.number_format($paid, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		return <<<_
			<div class="divider"></div>
			
			<div class="registrant-heading">
				Payments
			</div>
			
			<div class="registrant-details-section">
				<h3>All Payments For Group</h3>
				
				<table style="border-collapse:separate; border-spacing:15px 5px;">
					<tr>
						<td>Total Cost</td>
						<td>{$cost}</td>
					</tr>
					<tr>
						<td>Amount Tendered</td>
						<td>{$paid}</td>
					</tr>
					<tr>
						<td>Balance Due</td>
						<td>{$remainingBalance}</td>
					</tr>
				</table>
				
				<div class="sub-divider"></div>
				
				<div class="fragment-payments">
					<div>
						{$list->html()}
					</div>
					
					<div class="sub-divider"></div>
			
					{$add->html()}
				</div>
			</div>
_;
	}
}

?>