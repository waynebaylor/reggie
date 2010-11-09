<?php

class template_admin_EditPage extends template_AdminPage
{
	private $event;
	private $page;
	
	function __construct($event,$page) {
		parent::__construct('Edit '.$page['title'].' Page');
		
		$this->event = $event;
		$this->page = $page;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'Page',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code']
		));	
	}
	
	protected function getContent() {
		$edit = new fragment_page_Edit($this->page);
		$sections = new fragment_section_Sections($this->page);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.sections");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$sections->html()}
			</div>
_;
	}
}

?>