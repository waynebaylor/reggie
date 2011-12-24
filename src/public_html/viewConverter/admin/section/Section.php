<?php

class viewConverter_admin_section_Section extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Section';
	}
	
	protected function body() {
		$body = parent::body();
		
		$this->breadcrumbsParams = array(
			'eventId' => $this->event['id'],
			'pageId' => $this->section['pageId'],
			'sectionId' => $this->section['id']
		);
		
		$this->contentToFragment = array(
			model_ContentType::$REG_TYPE => new fragment_regType_RegTypes($this->event, $this->section),
			model_ContentType::$CONTACT_FIELD => new fragment_contactField_ContactFields($this->event, $this->section),
			model_ContentType::$REG_OPTION => new fragment_sectionRegOptionGroup_RegOptionGroups($this->event, $this->section),
			model_ContentType::$TEXT => new fragment_Empty(),
			model_ContentType::$VAR_QUANTITY_OPTION => new fragment_variableQuantityOption_VariableQuantityOptions($this->event, $this->section)
		);
		
		$edit = new fragment_section_Edit($this->section);
		$content = $this->contentToFragment[$this->section['contentType']['id']];
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regTypes");
				dojo.require("hhreg.admin.contactFields");
				dojo.require("hhreg.admin.regOptionGroups");
				dojo.require("hhreg.admin.variableQuantityOptions");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$content->html()}
			</div>
_;
		
		return $body;
	}
	
	public function getSaveSection($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getAddSection($properties) {
		$this->setProperties($properties);
		return new fragment_section_List($this->page);
	}
}

?>