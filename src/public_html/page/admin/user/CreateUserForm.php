
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
	<td colspan="2">
		<div class="sub-divider"></div>
	</td>
</tr>
<tr>
	<td class="label">General Roles</td>
	<td>
		<?php foreach($this->generalRoles as $role): ?>
		<div style="padding-bottom:5px;">
			<?php echo $this->HTML->checkbox(array(
				'label' => $role['name'].'<br><span style="color: #aaa;">'.$role['description'].'</span>',
				'name' => 'generalRoles[]',
				'value' => $role['id']
			)) ?>
		</div>
		<?php endforeach; ?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<div class="sub-divider"></div>
	</td>
</tr>
<tr>
	<td class="label">Event Roles</td>
	<td>
		<?php foreach($this->events as $event): ?>
		<div style="padding-bottom:10px;">
			<div style="background-color:#d3d3d3; padding:2px 5px;">
				(<?php echo $event['code'] ?>) 
				<span style="font-style:italic;">
					<?php echo $event['displayName'] ?>
				</span>
			</div>	
			<div style="padding:10px 0 0 20px;">
			<?php foreach($this->eventRoles as $role): ?>
				<div style="padding-bottom:5px;">
					<?php echo $this->HTML->checkbox(array(
						'label' => $role['name'].'<br><span style="color: #aaa;">'.$role['description'].'</span>',
						'name' => 'eventRoles[]',
						'value' => $event['id'].'_'.$role['id']
					)) ?>
				</div>
			<?php endforeach; ?>
			</div>
		</div>
		<?php endforeach; ?>
	</td>
</tr>
