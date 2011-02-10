<?php

class fragment_user_List extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Users</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Email</th>
						<th>Role</th>
						<th>Options</th>
					</tr>
					{$this->getUsers()}
				</table>
			</div>
_;
	}
	
	private function getUsers() {
		$html = '';
		
		$users = db_UserManager::getInstance()->findAll();
		foreach($users as $user) {
			$role = $user['isAdmin'] === 'T'? 'Admin' : '';
			
			$html .= <<<_
				<tr>
					<td>{$user['email']}</td>
					<td>{$role}</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/user/User',
							'parameters' => array(
								'a' => 'view',
								'id' => $user['id']
							)
						))}
						&nbsp;
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/user/User',
							'parameters' => array(
								'action' => 'removeUser',
								'id' => $user['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}

		return $html;
	}
}

?>