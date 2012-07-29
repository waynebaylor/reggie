<?php

class fragment_editRegistrations_payment_Add extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Payment', 
			'/admin/registration/Payment', 
			'addPayment', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$chooser = new fragment_payment_PaymentChooser($this->event, array(), true);
		return <<<_
			<tr>
				<td class="label required">Amount</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'regGroupId',
						'value' => $this->group['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					
					{$this->HTML->text(array(
						'name' => 'amount',
						'value' => '',
						'size' => 10
					))}
					
					<div class="sub-divider"></div>
				</td>
			</tr>
			<tr>
				<td class="label">Payment Status</td>
				<td>
					{$this->HTML->radios(array(
						'name' => 'paymentReceived',
						'value' => 'F',
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
			<tr>
				<td></td>
				<td>
					<div id="general-errors"></div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="sub-divider"></div>
					
					{$chooser->html()}
				</td>
			</tr>
_;
	}
}

?>