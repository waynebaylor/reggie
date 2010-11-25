<?php

class template_admin_MainMenu extends template_AdminPage
{
	function __construct() {
		parent::__construct('Main Menu');
	}

	protected function getBreadcrumbs() {
		return new fragment_Empty();	
	}
	
	protected function getContent() {
		$events = new fragment_event_Events();
		
		$currentUser = SessionUtil::getUser();
		$users = SecurityUtil::isAdmin($currentUser)? 
			new fragment_user_Users() : new fragment_Empty();
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.mainMenu");
			</script>
			
			<div id="content">
				{$events->html()}
				
				<div class="divider"></div>
				
				{$users->html()}
			</div>
_;
	}
}

?>