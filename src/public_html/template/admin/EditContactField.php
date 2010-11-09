<?php

class template_admin_EditContactField extends template_AdminPage
{
	private $field;
	private $event;
	
	function __construct($event, $field) {
		parent::__construct('Edit Information Field');
		
		$this->field = $field;
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'ContactField',
			'contactFieldId'       => $this->field['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_contactField_Edit($this->field, $this->event['regTypes']);
		$options = new fragment_contactFieldOption_Options($this->event, $this->field);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.editContactField");
				dojo.require("hhreg.admin.contactFieldOptions");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$options->html()}
			</div>
_;
	}
}

?>