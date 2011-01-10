<?php

class template_admin_EditVariableQuantity extends template_AdminPage
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct('Edit Variable Quantity Option');
	
		$this->event = $event;
		$this->option = $option;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'VariableQuantityOption',
			'id' => $this->option['id'],
			'eventId' => $this->event['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_variableQuantityOption_Edit($this->option);
		$prices = new fragment_regOptionPrice_Prices($this->event, $this->option);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regOptionPrices");
				dojo.require("hhreg.calendar");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$prices->html()}
			</div>
_;
	}
}

?>