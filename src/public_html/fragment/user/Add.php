<?php

class fragment_user_Add extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add User', 
			'/admin/user/User', 
			'addUser', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Email</td>
				<td>
					{$this->HTML->text(array(
						'name' => 'email',
						'value' => '',
						'maxlength' => 255
					))}
				</td>
			</tr>
			<tr>
				<td class="required label">Password</td>
				<td>
					<input type="password" name="password" value="">
				</td>
			</tr>
			<tr>
				<td class="label">Role</td>
				<td>
					{$this->HTML->checkbox(array(
						'label' => 'Admin',
						'name' => 'isAdmin',
						'value' => 'T'
					))}
				</td>
			</tr>
_;
	}
}

?>