<?php

class fragment_editRegistrations_payment_Edit extends template_Template
{
	private $payment;
	
	function __construct($payment) {
		parent::__construct();
		
		$this->payment = $payment;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/registration/Payment', 
			'savePayment', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Payment</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		if($this->payment['paymentTypeId'] == model_PaymentType::$CHECK) {
			return <<<_
				<tr>
					<td class="label required">Amount</td>
					<td>
						{$this->HTML->hidden(array(
							'name' => 'id',
							'value' => $this->payment['id']
						))}
						{$this->HTML->hidden(array(
							'name' => 'paymentType',
							'value' => $this->payment['paymentTypeId']
						))}
						
						{$this->HTML->text(array(
							'name' => 'amount',
							'value' => $this->payment['amount']
						))}
					</td>
				</tr>
				<tr>
					<td class="label required">Check Number</td>
					<td>
						{$this->HTML->text(array(
							'name' => 'checkNumber',
							'value' => $this->escapeHtml($this->payment['checkNumber'])
						))}
					</td>
				</tr>
				<tr>
					<td class="label">Payment Status</td>
					<td>
						{$this->HTML->radios(array(
							'name' => 'paymentReceived',
							'value' => $this->payment['paymentReceived'],
							'items' => array(
								array(
									'label' => 'Pending',
									'value' => 'F'
								),
								array(
									'label' => 'Paid',
									'value' => 'T'
								)
							)
						))}
					</td>
				</tr>
_;
		}
		else if($this->payment['paymentTypeId'] == model_PaymentType::$PO) {
			return <<<_
				<tr>
					<td class="label required">Amount</td>
					<td>
						{$this->HTML->hidden(array(
							'name' => 'id',
							'value' => $this->payment['id']
						))}
						{$this->HTML->hidden(array(
							'name' => 'paymentType',
							'value' => $this->payment['paymentTypeId']
						))}
						
						{$this->HTML->text(array(
							'name' => 'amount',
							'value' => $this->payment['amount']
						))}
					</td>
				</tr>
				<tr>
					<td class="label required">Purchase Order Number</td>
					<td>
						{$this->HTML->text(array(
							'name' => 'purchaseOrderNumber',
							'value' => $this->escapeHtml($this->payment['purchaseOrderNumber'])
						))}
					</td>
				</tr>
				<tr>
					<td class="label">Payment Status</td>
					<td>
						{$this->HTML->radios(array(
							'name' => 'paymentReceived',
							'value' => $this->payment['paymentReceived'],
							'items' => array(
								array(
									'label' => 'Pending',
									'value' => 'F'
								),
								array(
									'label' => 'Paid',
									'value' => 'T'
								)
							)
						))}
					</td>
				</tr>
_;
		}
	}
}

?>