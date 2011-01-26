<?php

class fragment_payment_AuthorizeNET extends template_Template
{
	private $eventPaymentType;
	private $values;
	private $selectedPaymentTypeId;
	
	function __construct($eventPaymentType, $values, $selectedPaymentTypeId) {
		parent::__construct();
		
		$this->eventPaymentType = $eventPaymentType;
		$this->values = $values;
		$this->selectedPaymentTypeId = $selectedPaymentTypeId;
	}
	
	public function html() {
		$showForm = 'hide';
		if(model_PaymentType::$AUTHORIZE_NET === $this->selectedPaymentTypeId) {
			$showForm = '';
		}
		
		return <<<_
			<div class="authorizeNet-payment-instructions {$showForm}">
				<div>{$this->eventPaymentType['instructions']}</div>
				<table class="auth-net-form">
					<tr>
						<td colspan="2" class="credit-card-title">
							Credit Card Information
						</td>
					</tr>
					<tr>
						<td class="label">Card Number</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'cardNumber',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'cardNumber', '')),
								'size' => '20',
								'maxlength' => '16'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Expiration Date</td>
						<td>
							{$this->getMonth()}
							{$this->getYear()}
						</td>
					</tr>
					<tr>
						<td colspan="2" class="billing-title">Billing Information</td>
					</tr>
					<tr>
						<td class="label">First Name</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'firstName',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'firstName', '')),
								'size' => '15'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'lastName',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'lastName', '')),
								'size' => '15'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Address</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'address',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'address', '')),
								'size' => '20'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">City</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'city',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'city', '')),
								'size' => '20'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">State</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'state',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'state', '')),
								'size' => '10'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Zip</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'zip',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'zip', '')),
								'size' => '10'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Country</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'country',
								'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'country', 'US')),
								'size' => '10'
							))}
						</td>
					</tr>
				</table>
			</div>
_;
	}
	
	private function getMonth() {
		return $this->HTML->select(array(
			'name' => 'month',
			'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'month', '01')),
			'items' => array(
				array('label' => '01', 'value' => '01'),
				array('label' => '02', 'value' => '02'),
				array('label' => '03', 'value' => '03'),
				array('label' => '04', 'value' => '04'),
				array('label' => '05', 'value' => '05'),
				array('label' => '06', 'value' => '06'),
				array('label' => '07', 'value' => '07'),
				array('label' => '08', 'value' => '08'),
				array('label' => '09', 'value' => '09'),
				array('label' => '10', 'value' => '10'),
				array('label' => '11', 'value' => '11'),
				array('label' => '12', 'value' => '12')
			)
		));
	}
	
	private function getYear() {
		$currentYear = date('Y');
		$years = array();
		for($i=0; $i<15; $i++) {
			$y = intval($currentYear, 10) + $i;
			$years[] = array(
				'label' => strval($y),
				'value' => strval($y)
			);
		}
		
		return $this->HTML->select(array(
			'name' => 'year',
			'value' => $this->escapeHtml(ArrayUtil::getValue($this->values, 'year', $currentYear)),
			'items' => $years
		));
	}
}

?>