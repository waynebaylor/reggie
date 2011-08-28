<?php

class viewConverter_admin_Login extends viewConverter_ViewConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Login';
	}
	
	protected function head() {
		return <<<_
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/admin.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/shared.less'))}
			
			{$this->HTML->script(array('src' => '/js/less.js'))}	
			{$this->HTML->script(array('src' => '/js/dojo/reggie_login.js'))}
_;
	}
	
	protected function body() {
		$body = $this->getFileContents('page_admin_Login');
		
		return $body;
	}
	
	public function getFormRows() {
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