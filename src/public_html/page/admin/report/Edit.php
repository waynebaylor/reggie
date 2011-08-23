
<tr>
	<td class="required label">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->report['id']
		)) ?>
		
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => $this->escapeHtml($this->report['name']),
			'maxlength' => 255
		)) ?>
	</td>
</tr>



