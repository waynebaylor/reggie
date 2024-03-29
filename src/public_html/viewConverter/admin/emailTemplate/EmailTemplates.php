<?php

class viewConverter_admin_emailTemplate_EmailTemplates extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Email Templates';
	}
	
	/**
	 * email templates are viewed from within a dijit tab.
	 */
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.EmailTemplateGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.EmailTemplateGrid({
						eventId: {$this->eventId}
					}, dojo.place("<div></div>", dojo.byId("email-template-grid"), "replace")).startup();
				});
			</script>
			
			<div id="email-template-grid">
				<div class="dijitContentPaneLoading" style="height:24px; width:24px;"></div> Loading...
			</div>
_;

		return new template_TemplateWrapper($html);
	}
	
	public function getListTemplates($properties) {
		$this->setProperties($properties);

		$html = $this->getFileContents('page_admin_emailTemplate_List');
		return new template_TemplateWrapper($html);
	}
	
	public function getDeleteTemplates($properties) {
		$this->setProperties($properties);
		return new fragment_Success();		
	}
}

?>