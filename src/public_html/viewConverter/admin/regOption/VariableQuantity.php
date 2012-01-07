<?php

class viewConverter_admin_regOption_VariableQuantity extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Variable Quantity Option';
	}

	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_variableQuantityOption_Edit($this->option);
		$prices = new fragment_regOptionPrice_Prices($this->event, $this->option);
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regOptionPrices");
				dojo.require("hhreg.calendar");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$prices->html()}
			</div>
_;

		return $body;
	}
	
	public function getAddOption($properties) {
		$this->setProperties($properties);
		return new fragment_variableQuantityOption_List($this->event, $this->section);
	}
	
	public function getRemoveOption($properties) {
		$this->setProperties($properties);
		return new fragment_variableQuantityOption_List($this->event, $this->section);
	}
	
	public function getSaveOption($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getMoveOptionUp($properties) {
		$this->setProperties($properties);
		return new fragment_variableQuantityOption_List($this->event, $this->section);
	}
	
	public function getMoveOptionDown($properties) {
		$this->setProperties($properties);
		return new fragment_variableQuantityOption_List($this->event, $this->section);
	}
}