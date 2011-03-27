
<tr>
	<td class="required label">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->event['id']
		)) ?>
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => '',
			'maxlength' => 255
		)) ?>
	</td>
</tr>






