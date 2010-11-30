<?php

class template_admin_EditGroupRegistration extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Edit Group Registration');
		
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'GroupRegistration',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_groupRegistration_Edit($this->event);
		$fields = new fragment_groupRegistration_field_Fields($this->event);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.groupRegistration");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$fields->html()}
			</div>
_;
	}
}

?>