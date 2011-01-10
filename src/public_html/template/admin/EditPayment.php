<?php

class template_admin_EditPayment extends template_AdminPage
{
	private $event;
	private $report;
	private $group;
	private $payment;
	
	function __construct($event, $report, $group, $payment) {
		parent::__construct('Edit Payment');
		
		$this->event = $event;
		$this->report = $report;
		$this->group = $group;
		$this->payment = $payment;
	}	
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'EditPayment',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code'],
			'reportName' => $this->report['name'],
			'reportId' => $this->report['id'],
			'groupId' => $this->group['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_editRegistrations_payment_Edit($this->payment);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>

			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
			</div>
_;
	}
}

?>