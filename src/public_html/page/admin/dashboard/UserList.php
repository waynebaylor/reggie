
<h3>Users</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Email</th>
			<th>Role</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->users as $user): ?>
		<tr>
			<td>
				<?php echo $user['email'] ?>
			</td>
			<td>
				<?php echo ($user['isAdmin'] === 'T')? 'Admin' : '' ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/user/User',
					'parameters' => array(
						'a' => 'view',
						'id' => $user['id']
					)
				)) ?>
				&nbsp;
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/dashboard/MainMenu',
					'parameters' => array(
						'action' => 'removeUser',
						'id' => $user['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>

