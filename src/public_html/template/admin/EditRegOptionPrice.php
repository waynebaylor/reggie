<?php

class template_admin_EditRegOptionPrice extends template_AdminPage
{
	private $price;
	private $event;
	
	function __construct($event, $price) {
		parent::__construct('Edit Option Price');
		
		$this->price = $price;	
		$this->event = $event;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'RegOptionPrice',
			'id' => $this->price['id'],
			'eventId' => $this->event['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_regOptionPrice_Edit($this->price, $this->event['regTypes']);

		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.calendar");
			</script>
			
			<div id="content">
				{$edit->html()}
			</div>
_;
	}
}

?>