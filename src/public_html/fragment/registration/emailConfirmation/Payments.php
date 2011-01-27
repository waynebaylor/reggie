<?php

class fragment_registration_emailConfirmation_Payments extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		$html = '';
		
		$cost = db_reg_GroupManager::getInstance()->findTotalCost($this->group['id']);
		$paid = db_reg_GroupManager::getInstance()->findTotalPaid($this->group['id']);
		$remainingBalance = $cost - $paid;
		
		$cost = '$'.number_format($cost, 2);
		$paid = '$'.number_format($paid, 2);
		$remainingBalance = '$'.number_format($remainingBalance, 2);
		
		foreach($this->group['payments'] as $payment) {
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
					<td>&nbsp;&nbsp;&nbsp;{$date}</td>
					<td>{$type}</td>
					<td>{$amount}</td>
				</tr>
_;
		}
		
		if(empty($html)) {
			$html = '<td colspan="3">No payment.</td>';
		}
		
		return <<<_
			<br/>
			<div style="font-size:20px; font-weight:bold;">Payments</div>
			<br/>
			
			<table style="border-collapse:separate; border-spacing:10px 5px;">
				<tr>
					<td colspan="2" style="font-weight:bold;">Total Cost</td>
					<td>{$cost}</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" style="font-weight:bold;">Payment Information</td>
					<td></td>
				</tr>
				<tr>
					{$html}
				</tr>
				<tr>
					<td colspan="2" style="font-weight:bold;">Amount Tendered</td>
					<td>{$paid}</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2" style="font-weight:bold;">Balance Due</td>
					<td>{$remainingBalance}</td>
				</tr>
			</table>
_;
	}
}

?>