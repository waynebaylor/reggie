<?php

class fragment_editRegistrations_Payment extends template_Template
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
		
		$payments = db_reg_PaymentManager::getInstance()->findByRegistrationGroup($this->group);
		foreach($payments as $payment) {
			$html .= $this->getPaymentRow($payment);
		}
		
		return <<<_
			<div class="registrant-heading">
				Payments
			</div>
			
			<table>
				<tr>
					<th>Date</th>
					<th>Payment Information</th>
					<th>Amount</th>
					<th></th>
				</tr>
				{$html}
			</table>
_;
	}
	
	private function getPaymentRow($payment) {
		$amount = '$'.number_format($payment['amount'], 2);
		
		switch($payment['paymentTypeId']) {
			case model_PaymentType::$CHECK:
				return <<<_
					<tr>
						<td>
							{$payment['transactionDate']}
						</td>
						<td>
							Check Number: {$payment['checkNumber']}
						</td>
						<td>
							{$amount}
						</td>
						<td>
							{$this->HTML->checkbox(array(
								'label' => 'Payment Received',
								'name' => $payment['id'].'_paymentReceived',
								'value' => 'true',
								'checked' => $payment['paymentReceived'] === 'true'
							))}
						</td>
					</tr>
_;
			case model_PaymentType::$PO:
				return <<<_
					<tr>
						<td>
							{$payment['transactionDate']}
						</td>
						<td>
							Purchase Order Number: {$paymnent['purchaseOrderNumber']}
						</td>
						<td>
							{$amount}
						</td>
						<td>
							{$this->HTML->checkbox(array(
								'label' => 'Payment Received',
								'name' => $payment['id'].'_paymentReceived',
								'value' => 'true',
								'checked' => $payment['paymentReceived'] === 'true'
							))}
						</td>
					</tr>
_;
			case model_PaymentType::$AUTHORIZE_NET:
				return $this->getAuthorizeNetRow($payment);
		}
	}
	
	private function getAuthorizeNetRow($payment) {
		return <<<_
					<tr>
						<td>
							{$payment['transactionDate']}
						</td>
						<td>
							<table>
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
										{$payment['city']} {$payment['state']} {$payment['zip']}
										<br/>
										{$payment['country']}
									</td>
								</tr>
							</table>
						</td>
						<td>
							{$amount}
						</td>
						<td></td>
					</tr>
_;
	}
}

?>