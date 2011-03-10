
<tr>
	<td class="required label">Email</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'email',
			'value' => '',
			'maxlength' => 255
		)) ?>
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
		<?php echo $this->HTML->checkbox(array(
			'label' => 'Admin',
			'name' => 'isAdmin',
			'value' => 'T'
		)) ?>
	</td>
</tr>




