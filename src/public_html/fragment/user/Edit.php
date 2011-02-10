<?php

class fragment_user_Edit extends template_Template
{
	private $user;

	function __construct($user) {
		parent::__construct();
		
		$this->user = $user;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/user/User', 
			'saveUser', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-edit">
				<h3>Edit User</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Email</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->escapeHtml($this->user['id'])
					))}
					
					{$this->HTML->text(array(
						'name' => 'email',
						'value' => $this->escapeHtml($this->user['email']),
						'maxlength' => 255
					))}
				</td>
			</tr>
			<tr>
				<td class="label">New Password</td>
				<td>
					<input type="password" name="password" value="">
				</td>
			</tr>
			<tr>
				<td class="label">Role</td>
				<td>
					{$this->HTML->checkbox(array(
						'checked' => $this->user['isAdmin'] === 'T',
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