<?php

class template_admin_EditPayment extends template_AdminPage
{
	private $payment;
	
	function __construct($payment) {
		parent::__construct('Edit Payment');
		
		$this->payment = $payment;
	}	
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
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