
<tr>
	<td class="label">Title</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'displayName',
			'value' => '',
			'size' => '50',
			'maxlength' => '255'
		)) ?>
	</td>
</tr>
<tr>
	<td class=" required label">Code</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'code',
			'value' => '',
			'maxlength' => '255'
		)) ?>
	</td>
</tr>
<tr>
	<td class="required label">Registration Open</td>
	<td>
		<?php echo $this->HTML->calendar(array(
			'name' => 'regOpen',
			'value' => '',
			'size' => '16',
			'maxlength' => '16'
		)) ?>
	</td>
</tr>
<tr>
	<td class="required label">Registration Closed</td>
	<td>
		<?php echo $this->HTML->calendar(array(
			'name' => 'regClosed',
			'value' => '',
			'size' => '16',
			'maxlength' => '16'
		)) ?>
	</td>
</tr>
			




