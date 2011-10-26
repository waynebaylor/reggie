<?php

class viewConverter_admin_page_Page extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Edit Page';
	}
	
	protected function body() {
		$body = parent::body();
		
		$edit = new fragment_page_Edit($this->page);
		$sections = new fragment_section_Sections($this->page);
		
		$body .= <<<_
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
		
		return $body;
	}
	
	public function getSavePage($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>