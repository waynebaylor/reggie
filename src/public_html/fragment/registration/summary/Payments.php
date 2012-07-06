<?php

class fragment_registration_summary_Payments extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group, $isPdfFormat = FALSE) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
		$this->isPdfFormat = $isPdfFormat;
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
				if(SessionUtil::getUser()) {
					$type = <<<_
						{$payment['name']}
						<br>{$type}
						<br>Transaction ID: {$payment['transactionId']}
_;
				}
			}
			
			$amount = '$'.number_format($payment['amount'], 2);
			
			$html .= <<<_
				<tr>
					<td style="vertical-align:top; padding-bottom:15px;">{$date}</td>
					<td style="vertical-align:top; padding-bottom:15px;">{$type}</td>
					<td style="vertical-align:top; padding-bottom:15px;">{$amount}</td>
				</tr>
_;
		}
		
		if($this->isPdfFormat) {
			$heading = <<<_
				<br><br>
				<table>
					<tr><td></td></tr>
					<tr><td style="font-weight:bold; background-color:#ccc;">
						<br><br>
						&nbsp;Payments
						<br>
					</td></tr>
				</table>
_;
		}
		else {
			$heading = <<<_
				<div class="sub-divider"></div>
			
				<div class="registrant-heading">Payments</div>
_;
		}
		
		return <<<_
			{$heading}
			
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