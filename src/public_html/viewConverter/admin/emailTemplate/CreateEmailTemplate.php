<?php

class viewConverter_admin_emailTemplate_CreateEmailTemplate extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Create Email Template';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/emailTemplate/CreateEmailTemplate',
			'action' => 'createTemplate',
			'rows' => $this->getFileContents('page_admin_emailTemplate_Edit'),
			'redirectUrl' => "/admin/event/EditEvent?eventId={$this->eventId}&showTab=emailTemplates"
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