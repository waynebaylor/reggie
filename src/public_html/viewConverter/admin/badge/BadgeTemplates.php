<?php

class viewConverter_admin_badge_BadgeTemplates extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Badge Templates';
	}
	
	protected function body() {
		$body = parent::body();
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.BadgeTemplateGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.BadgeTemplateGrid({
						eventId: {$this->eventId}
					}, dojo.place("<div></div>", dojo.byId("badge-template-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div id="badge-template-grid"></div>
				</div>
			</div>
_;
		
		return $body;
	}
	
	public function getListTemplates($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_data_BadgeTemplates'));
	}
	
	public function getAddTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_TemplateList'));
	}
	
	public function getRemoveTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_TemplateWrapper($this->getFileContents('page_admin_badge_TemplateList'));
	}
	
	public function getCopyTemplate($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect('/admin/badge/BadgeTemplates?a=view&eventId='.$this->eventId);
	}
}

?>