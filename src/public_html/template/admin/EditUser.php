<?php

class template_admin_EditUser extends template_AdminPage
{
	private $user;
	
	function __construct($user) {
		parent::__construct('Edit User');
		
		$this->user = $user;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'User'
		));
	}
	
	protected function getContent() {
		$edit = new fragment_user_Edit($this->user);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>	
			
			<div id="content">
				{$edit->html()}
			</div>	
_;
	}
}

?>