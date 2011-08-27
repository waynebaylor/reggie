<?php

class template_admin_Login extends template_AdminPage
{
	function __construct() {
		parent::__construct('Login');
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
	}
	
	protected function getContent() {
		$form = new fragment_XhrTableForm(array(
			'url' => '/admin/Login', 
			'action' => 'login', 
			'rows' => $this->getFormRows(),
			'buttonText' => 'Submit',
			'errorText' => 'There was a problem. Please try again.',
			'redirectUrl' => '/admin/Login'
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				
				dojo.addOnLoad(function() {
					document.getElementsByName("email")[0].focus();
				});				
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>Please Log In</h3>
		
					{$form->html()}
				
				</div>
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td colspan="2">
					<div id="general-errors"></div>
				</td>
			</tr>
			<tr>
				<td class="required label">Email</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'email',
						'value' => '',
						'size' => 30
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Password</td>
				<td>
					<input type="password" name="password" value="" size="30">
				</td>
			</tr>
_;
	}
}

?>