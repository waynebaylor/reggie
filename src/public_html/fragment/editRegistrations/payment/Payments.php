<?php

class fragment_editRegistrations_payment_Payments extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/registration/Registration', 
			'savePaymentReceived', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="divider"></div>
			
			<div class="registrant-heading">
				Payments
			</div>
			
			<div class="fragment-edit">
				<h3>All Payments For Group</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		$list = new fragment_editRegistrations_payment_List($this->event, $this->group);
		//$add = new fragment_editRegistrations_payment_Add($this->event, $this->group);
		
		return <<<_
			<tr>
				<td></td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'regGroupId',
						'value' => $this->group['id']
					))}
					
					<div class="fragment-payments">
						<div>
							{$list->html()}
						</div>
					</div>
				</td>
			</tr>
_;
	}
}

?>