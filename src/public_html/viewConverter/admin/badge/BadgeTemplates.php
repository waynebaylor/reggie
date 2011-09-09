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
				dojo.require("hhreg.admin.printBadges");
				dojo.require("hhreg.admin.widget.BadgeTemplateGrid");
				
				dojo.addOnLoad(function() {
					hhreg.admin.printBadges.setup();
					
					new hhreg.admin.widget.BadgeTemplateGrid({
						eventId: {$this->eventId}
					}, dojo.place("<div></div>", dojo.byId("badge-template-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div style="padding-left:15px;">
						{$this->HTML->link(array(
							'label' => 'Create New Template',
							'href' => '/admin/badge/CreateBadgeTemplate',
							'parameters' => array(
								'eventId' => $this->eventId
							)
						))}
						&nbsp;&nbsp;
						<span id="print-badges-link" class="link">Print Badges</span>
						<div id="print-badges-form" class="hide">
							{$this->tableForm(
								'/admin/badge/PrintBadge',
								'batchCount',
								$this->getFileContents('page_admin_badge_PrintBadgeForm'),
								'Print Badges'
							)}
						</div>
					</div>
					
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
	
	public function getDeleteTemplates($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
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