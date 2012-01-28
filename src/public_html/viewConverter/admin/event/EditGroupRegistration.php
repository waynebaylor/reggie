<?php

class viewConverter_admin_event_EditGroupRegistration extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title ='Edit Group Registration';
	}
	
	public function getView($properties) {
		$this->setProperties($properties);

		$html = $this->getContent();
		
		return new template_TemplateWrapper($html);
	}
	
	public function getSaveGroupReg($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getAddField($properties) {
		$this->setProperties($properties);
		return new fragment_groupRegistration_field_List($this->event);
	}
	
	public function getRemoveField($properties) {
		$this->setProperties($properties);
		return new fragment_groupRegistration_field_List($this->event);
	}
	
	private function getContent() {
		$edit = new fragment_groupRegistration_Edit($this->event);
		$fields = new fragment_groupRegistration_field_Fields($this->event);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.groupRegistration");
			</script>
			
			{$edit->html()}
			
			<div class="divider"></div>
			
			{$fields->html()}
_;
	}
}

?>