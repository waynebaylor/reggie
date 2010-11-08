<?php

class template_admin_MainMenu extends template_AdminPage
{
	function __construct() {
		parent::__construct('Administration Menu');
	}

	protected function getContent() {
		$events = new fragment_event_Events();
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.events");
			</script>
			
			<div id="content">
				{$events->html()}
			</div>
_;
	}
}

?>