<?php

class viewConverter_admin_staticPage_PageList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Event Pages';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.StaticPageGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.StaticPageGrid({
						eventId: {$this->eventId}
					}, dojo.place("<div></div>", dojo.byId("page-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div id="page-grid"></div>
				</div>
			</div>
_;

		return $body;
	}
	
	public function getListPages($properties) {
		$this->setProperties($properties);
		
		$html = $this->getFileContents('page_admin_data_StaticPages');
		return new template_TemplateWrapper($html);
	}
	
	public function getDeletePages($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>