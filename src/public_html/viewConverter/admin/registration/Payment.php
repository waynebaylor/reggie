<?php

class viewConverter_admin_registration_Payment extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Payment';
	}
	
	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_editRegistrations_payment_Edit($this->payment);
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>

			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
			</div>
_;
		
		return $body;
	}
	
	public function getSavePayment($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getAddPayment($properties) {
		$this->setProperties($properties);
		return new fragment_editRegistrations_payment_List($this->group);
	}
	
	public function getRemovePayment($properties) {
		$this->setProperties($properties);
		return new fragment_editRegistrations_payment_List($this->group);
	}
}

?>