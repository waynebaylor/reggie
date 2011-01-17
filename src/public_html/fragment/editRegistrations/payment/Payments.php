<?php

class fragment_editRegistrations_payment_Payments extends template_Template
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
		$list = new fragment_editRegistrations_payment_List($this->event, $this->report, $this->group);
		$add = new fragment_editRegistrations_payment_Add($this->event, $this->report, $this->group);
		
		return <<<_
			<div class="divider"></div>
			
			<div class="registrant-heading">
				Payments
			</div>
			
			<div class="registrant-details-section">
				<h3>All Payments For Group</h3>
				
				<div class="fragment-payments">
					<div>
						{$list->html()}
					</div>
					
					<div class="sub-divider"></div>
			
					{$add->html()}
				</div>
			</div>
_;
	}
}

?>