<?php

class template_admin_EditSection extends template_AdminPage
{
	private $event;
	private $section;
	private $contentToFragment;
	
	function __construct($event, $section) {
		parent::__construct('Edit Section');
		
		$this->event = $event;
		$this->section = $section;
		
		$this->contentToFragment = array(
			model_ContentType::$REG_TYPE => new fragment_regType_RegTypes($event, $section),
			model_ContentType::$CONTACT_FIELD => new fragment_contactField_ContactFields($event, $section),
			model_ContentType::$REG_OPTION => new fragment_sectionRegOptionGroup_RegOptionGroups($event, $section),
			model_ContentType::$TEXT => new fragment_Empty(),
			model_ContentType::$VAR_QUANTITY_OPTION => new fragment_variableQuantityOption_VariableQuantityOptions($event, $section)
		);
	}

	protected function getContent() {
		$edit = new fragment_section_Edit($this->section);
		$content = $this->contentToFragment[$this->section['contentType']['id']];
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'Section',
			'id' => $this->section['id'],
			'eventId' => $this->event['id']
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regTypes");
				dojo.require("hhreg.admin.contactFields");
				dojo.require("hhreg.admin.regOptionGroups");
				dojo.require("hhreg.admin.variableQuantityOptions");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$content->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}	
}

?>