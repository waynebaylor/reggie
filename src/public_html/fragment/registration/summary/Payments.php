<?php

class fragment_registration_summary_Payments extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		return $this->getPayments($this->group);
	}
	
	private function getPayments($group) {
		$html = '';
		
		$cost = db_reg_GroupManager::getInstance()->findTotalCost($group['id']);
		$paid = db_reg_GroupManager::getInstance()->findTotalPaid($group['id']);
		$remainingBalance = $cost - $paid;
		
		$cost = '$'.number_format($cost, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		foreach($group['payments'] as $payment) {
			$date = substr($payment['transactionDate'], 0, 10);
			
			$paymentType =	model_PaymentType::valueOf($payment['paymentTypeId']); 
			
			$received = ($payment['paymentReceived'] === 'true')? '(Received)' : '(Not yet received)';
				
			if($paymentType['id'] == model_PaymentType::$CHECK) {
				$type = "{$paymentType['displayName']} {$payment['checkNumber']} {$received}";
			}
			else if($paymentType['id'] == model_PaymentType::$PO) {
				$type = "{$paymentType['displayName']} {$payment['purchaseOrderNumber']} {$received}";
			}
			else if($paymentType['id'] == model_PaymentType::$AUTHORIZE_NET) {
				$type = $payment['cardType'].' '.$payment['cardSuffix'];
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
