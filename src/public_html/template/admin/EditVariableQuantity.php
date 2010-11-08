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
	
	protected function getContent() {
		$edit = new fragment_variableQuantityOption_Edit($this->option);
		$prices = new fragment_regOptionPrice_Prices($this->event, $this->option);
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'VariableQuantityOption',
			'id' => $this->option['id'],
			'eventId' => $this->event['id']
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regOptionPrices");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$prices->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
}

?>