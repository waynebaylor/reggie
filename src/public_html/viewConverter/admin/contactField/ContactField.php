<?php

class viewConverter_admin_contactField_ContactField extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Information Field';
	}
	
	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_contactField_Edit($this->field, $this->event['regTypes']);
		$options = new fragment_contactFieldOption_Options($this->event, $this->field);
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.editContactField");
				dojo.require("hhreg.admin.contactFieldOptions");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$options->html()}
			</div>
_;
		
		return $body;
	}
	
	public function getAddField($properties) {
		$this->setProperties($properties);
		return new fragment_contactField_List($this->event, $this->section);
	} 
	
	public function getRemoveField($properties) {
		$this->setProperties($properties);
		return new fragment_contactField_List($this->event, $this->section);
	}
	
	public function getMoveFieldUp($properties) {
		$this->setProperties($properties);
		return new fragment_contactField_List($this->event, $this->section);
	}
	
	public function getMoveFieldDown($properties) {
		$this->setProperties($properties);
		return new fragment_contactField_List($this->event, $this->section);
	}
	
	public function getSave($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>