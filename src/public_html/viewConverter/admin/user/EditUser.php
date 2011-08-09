<?php

class viewConverter_admin_user_EditUser extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit User';
	}
	
	protected function body() {
		$body = parent::body();
		
		$formHtml = $this->xhrTableForm(array(
			'url' => '/admin/user/EditUser', 
			'action' => 'saveUser', 
			'rows' => $this->getFileContents('page_admin_user_CreateUserForm'),
			'useAjax' => false
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
	
	public function getSaveUser($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect('/admin/dashboard/Users');
	}
}

?>