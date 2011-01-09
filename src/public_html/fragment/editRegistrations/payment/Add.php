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
		$chooser = new fragment_payment_PaymentChooser($this->event, array());
		return <<<_
			<tr>
				<td></td>
				<td class="label required">
					{$this->HTML->hidden(array(
						'name' => 'regGroupId',
						'value' => $this->group['id']
					))}
					
					Amount 
					{$this->HTML->text(array(
						'name' => 'amount',
						'value' => '',
						'size' => 10
					))}
					
					<div class="sub-divider"></div>
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					{$chooser->html()}
				</td>
			</tr>
_;
	}
}

?>