<?php

class viewConverter_admin_staticPage_EditPage extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		$this->title = 'Edit Page Content';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/staticPage/EditPage',
			'action' => 'savePage',
			'rows' => $this->getFileContents('page_admin_staticPage_CreatePageForm'),
			'redirectUrl' => "/admin/event/Manage?eventId={$this->eventId}"
		));
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.staticPage");
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
	
	public function getSavePage($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
}

?>