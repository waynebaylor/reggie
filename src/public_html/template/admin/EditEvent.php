<?php

class template_admin_EditEvent extends template_AdminPage
{
	private $event;
	
	function __construct($event) {
		parent::__construct('Edit Event '.$event['code']);

		$this->event = $event;
	}
	
	protected function getContent() {	
		$edit = new fragment_event_Edit($this->event);
		$pages = new fragment_page_Pages($this->event);
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'Event',
			'event' => $this->event
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.eventPages");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$pages->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
}

?>