<?php

class template_admin_GroupSummary extends template_AdminPage
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct('Summary');
		
		$this->event = $event;
		$this->group = $group;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
	}
	
	protected function getContent() {
		return <<<_
			<div id="content">
				<div class="registrant-details-section">
					{$this->getRegistrants()}
					
					{$this->getPayments()}
				</div>
			</div>
_;
	}
	
	private function getRegistrants() {
		$html = '';
		
		foreach($this->group['registrations'] as $registration) {
			$html .= <<<_
				<div class="sub-divider"></div>
				
				<div class="registrant-heading">Registrant</div>
				
				<table class="summary">
					<tr>
						<td style="width:50%;">
							{$this->getInformation($registration)}
						</td>
						<td style="width:50%;">
							{$this->getOptions($registration)}
						</td>
					</tr>
				</table>
_;
		}
		
		return $html;
	}
	
	private function getInformation($r) {
		$info = new fragment_groupSummary_Information($this->event, $r);
		
		$date = substr($r['dateRegistered'], 0, 10);
		
		$regType = db_RegTypeManager::getInstance()->find($r['regTypeId']);
		
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
				{$info->html()}
			</table>	
_;
	}
	
	private function getOptions($r) {
		$opts = new fragment_groupSummary_RegOptions($this->event, $r);
		$variable = new fragment_groupSummary_VariableQuantity($this->event, $r);
		
		return <<<_
			<table>
				{$opts->html()}
				
				{$variable->html()}
			</table>	
_;
	}
	
	private function getPayments() {
		$html = '';
		
		$cost = db_reg_GroupManager::getInstance()->findTotalCost($this->group['id']);
		$paid = db_reg_GroupManager::getInstance()->findTotalPaid($this->group['id']);
		$remainingBalance = $cost - $paid;
		
		$cost = '$'.number_format($cost, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		foreach($this->group['payments'] as $payment) {
			$date = substr($payment['transactionDate'], 0, 10);
			
			$paymentType =	model_PaymentType::valueOf($payment['paymentTypeId']); 
			$type = $paymentType['displayName'];
			if($payment['paymentReceived'] !== 'true') {
				$type .= ' (Not yet received)';
			}
			
			if($paymentType['id'] == model_PaymentType::$AUTHORIZE_NET) {
				$type = $payment['cardType'];
			}
			
			$amount = '$'.number_format($payment['amount'], 2);
			
			$html .= <<<_
				<tr>
					<td>{$date}</td>
					<td>{$type}</td>
					<td>{$amount}</td>
				</tr>
_;
		}
		
		return <<<_
			<div class="sub-divider"></div>
			
			<div class="registrant-heading">Payments</div>
			
			<table class="summary">
				<tr>
					<td class="label" colspan="2" style="border-bottom:1px solid black;">Total Cost</td>
					<td style="border-bottom:1px solid black;">{$cost}</td>
				</tr>
				{$html}
				<tr>
					<td class="label" colspan="2" style="border-top:1px solid black;">Remaining balance</td>
					<td style="border-top:1px solid black;">{$remainingBalance}</td>
				</tr>
			</table>
_;
	}
}

?>