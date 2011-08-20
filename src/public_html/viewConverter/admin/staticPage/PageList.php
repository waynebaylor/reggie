<?php

class viewConverter_admin_staticPage_PageList extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = <<<_
			<div id="page-grid"></div>
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.StaticPageGrid");
				
				new hhreg.admin.widget.StaticPageGrid({
					eventId: {$this->eventId}
				}, dojo.place("<div></div>", dojo.byId("page-grid"), "replace")).startup();
			</script>
_;

		return new template_TemplateWrapper($html);
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