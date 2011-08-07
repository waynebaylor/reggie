<?php

class viewConverter_admin_user_User extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getCreateUserForm($properties) {
		$this->setProperties($properties);
		$this->title = 'Create User';
		
		$formHtml = $this->xhrTableForm(
			'/admin/user/User', 
			'createUser', 
			$this->getFileContents('page_admin_user_CreateUserForm')
		);
		
		$html = <<<_
			<div id="content">
				<h3>Create User</h3>
				{$formHtml}
			</div>		
_;
		
		return new template_TemplateWrapper($html);
	}
}

?>