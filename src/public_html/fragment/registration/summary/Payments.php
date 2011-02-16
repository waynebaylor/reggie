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
		$paid = '$'.number_format($paid, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		foreach($group['payments'] as $payment) {
			$date = substr($payment['transactionDate'], 0, 10);
			
			$paymentType =	model_PaymentType::valueOf($payment['paymentTypeId']); 
			
			$received = ($payment['paymentReceived'] === 'T')? '(Received)' : '(Pending)';
				
			if($paymentType['id'] == model_PaymentType::$CHECK) {
				$type = "{$paymentType['displayName']} {$payment['checkNumber']} {$received}";
			}
			else if($paymentType['id'] == model_PaymentType::$PO) {
				$type = "{$paymentType['displayName']} {$payment['purchaseOrderNumber']} {$received}";
			}
			else if($paymentType['id'] == model_PaymentType::$AUTHORIZE_NET) {
				$type = "{$payment['cardType']} {$payment['cardSuffix']}";
				// admin's can see the transaction id on the group summary page, 
				// but it shouldn't show on the user confirmation page.
				if(SecurityUtil::isAdmin(SessionUtil::getUser())) {
					$type .= "<br>Transaction ID: {$payment['transactionId']}";
				}
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
			
			<table style="border-collapse:separate; border-spacing:15px 5px;">
				{$html}
			</table>
			
			<div class="sub-divider"></div>
			
			<table style="border-collapse:collapse;">
				<tr>
					<td class="label" style="padding:0 15px 5px;">Total Cost</td>
					<td>{$cost}</td>
				</tr>
				<tr>
					<td class="label" style="padding:0 15px 5px;">Amount Tendered</td>
					<td>{$paid}</td>
				</tr>
				<tr>
					<td class="label" style="font-weight:bold; padding:0 15px 5px; border-top:1px solid black;">Balance Due</td>
					<td style="border-top:1px solid black;">{$remainingBalance}</td>
				</tr>
			</table>
_;
	}
}

?>
