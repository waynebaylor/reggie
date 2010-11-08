<?php

class template_admin_EditPage extends template_AdminPage
{
	private $page;
	
	function __construct($page) {
		parent::__construct('Edit '.$page['title'].' Page');
		
		$this->page = $page;
	}
	
	protected function getContent() {
		$edit = new fragment_page_Edit($this->page);
		$sections = new fragment_section_Sections($this->page);
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'Page',
			'id' => $this->page['id'],
			'eventId' => $this->page['eventId']
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.sections");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$sections->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
}

?>