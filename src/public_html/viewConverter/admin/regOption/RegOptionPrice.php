<?php

class viewConverter_admin_regOption_RegOptionPrice extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Option Price';
	}
	
	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_regOptionPrice_Edit($this->price, $this->event['regTypes']);

		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.calendar");
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}
			</div>
_;
		
		return $body;
	}
	
	public function getAddRegOptionPrice($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionPrice_List($this->event, $this->option);
	}
	
	public function getAddVariableQuantityPrice($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionPrice_List($this->event, $this->option);
	}
	
	public function getRemovePrice($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionPrice_List($this->event, $this->option);
	}
	
	public function getSavePrice($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}