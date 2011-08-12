<?php

class viewConverter_admin_event_CreateEvent extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Create Event';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/event/CreateEvent',
			'action' => 'createEvent',
			'rows' => $this->getFileContents('page_admin_event_CreateEventForm'),
			'redirectUrl' => '/admin/dashboard/Events'
		));
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.calendar");
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
	
	public function getCreateEvent($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>