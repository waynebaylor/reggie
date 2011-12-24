<?php

class viewConverter_admin_regType_RegType extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Registration Type';
	}
	
	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_regType_Edit($this->regType);
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}
			</div>
_;
		
		return $body;
	}
	
	public function getSaveRegType($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getAddRegType($properties) {
		$this->setProperties($properties);
		return new fragment_regType_List($this->event, $this->section);
	}
	
	public function getRemoveRegType($properties) {
		$this->setProperties($properties);
		return new fragment_regType_List($this->event, $this->section);
	}
	
	public function getMoveRegTypeUp($properties) {
		$this->setProperties($properties);
		return new fragment_regType_List($this->event, $this->section);
	}
	
	public function getMoveRegTypeDown($properties) {
		$this->setProperties($properties);
		return new fragment_regType_List($this->event, $this->section);
	}
}

?>