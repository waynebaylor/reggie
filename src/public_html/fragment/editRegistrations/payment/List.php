<?php

class fragment_editRegistrations_payment_List extends template_Template
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
		return <<<_
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Date</th>
						<th>Payment Information</th>
						<th>Amount</th>
						<th>Payment Status</th>
						<th>Options</th>
					</tr>
					{$this->getPayments()}
				</table>
			</div>
_;
	}
	
	private function getPayments() {
		$html = '';
		
		$payments = db_reg_PaymentManager::getInstance()->findByRegistrationGroup($this->group);
		foreach($payments as $payment) {
			$html .= $this->getPaymentRow($payment);
		}
		
		return $html;
	}
	
	public function getPaymentRow($payment) {
		$date = substr($payment['transactionDate'], 0, 10);
		
		$amount = '$'.number_format($payment['amount'], 2);
		
		$received = ($payment['paymentReceived'] === 'true')? 'Received' : 'Pending';
							
		$editLink = '';
		if(in_array($payment['paymentTypeId'], array(model_PaymentType::$CHECK, model_PaymentType::$PO))) {
			$editLink = $this->HTML->link(array(
				'label' => 'Edit',
				'href' => '/admin/registration/Payment',
				'parameters' => array(
					'a' => 'view',
					'id' => $payment['id'],
					'groupId' => $this->group['id'],
					'reportId' => $this->report['id']
				)
			));
		}
		
		return <<<_
			<tr>
				<td class="label">
					{$date}
				</td>
				<td class="label">
					{$this->getPaymentInfo($payment)}
				</td>
				<td class="label">
					{$amount}
				</td>
				<td class="label">
					{$received}
				</td>
				<td>
					{$editLink}
				</td>
			</tr>
_;
	}
	
	private function getPaymentInfo($payment) {
		switch($payment['paymentTypeId']) {
			case model_PaymentType::$CHECK:
				return $this->getCheckInfo($payment);
				
			case model_PaymentType::$PO:
				return $this->getPoInfo($payment);
			
			case model_PaymentType::$AUTHORIZE_NET:
				return $this->getAuthorizeNetInfo($payment);
			
			default:
				return '';
		}
	}
	
	private function getCheckInfo($payment) {
		return "Check Number: {$payment['checkNumber']}";
	}
	
	private function getPoInfo($payment) {
		return "Purchase Order Number: {$payment['purchaseOrderNumber']}";
	}
	
	private function getAuthorizeNetInfo($payment) {
		return <<<_
			<table style="background-color:inherit;">
				<tr>
					<td>Card Type:</td>
					<td>{$payment['cardType']}</td>
				</tr>
				<tr>
					<td>Last 4 Digits:</td>
					<td>{$payment['cardSuffix']}</td>
				</tr>
				<tr>
					<td>Name:</td>
					<td>{$payment['name']}</td>
				</tr>
				<tr>
					<td>Address:</td>
					<td>
						{$payment['address']}
						<br/>
						{$payment['city']}, {$payment['state']} {$payment['zip']}
						<br/>
						{$payment['country']}
					</td>
				</tr>
				<tr>
					<td>Transaction ID:</td>
					<td>{$payment['transactionId']}</td>
				</tr>
			</table>
_;
	}
}
?>