<?php

class viewConverter_admin_report_CreateReport extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Create Report';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/report/CreateReport',
			'action' => 'createReport',
			'rows' => $this->getFileContents('page_admin_report_Edit'),
			'redirectUrl' => "/admin/report/ReportList?eventId={$this->eventId}"
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
	
	public function getCreateReport($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>