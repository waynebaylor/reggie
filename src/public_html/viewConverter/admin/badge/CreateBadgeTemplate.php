<?php

class viewConverter_admin_badge_CreateBadgeTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Create Badge Template';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/badge/CreateBadgeTemplate',
			'action' => 'createTemplate',
			'rows' => $this->getFileContents('page_admin_badge_CreateTemplateForm'),
			'redirectUrl' => "/admin/badge/BadgeTemplates?eventId={$this->eventId}"
		));
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					{$formHtml}
				</div>
			</div> 	
_;
		
		return $body;
	}
	
	public function getCreateTemplate($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>