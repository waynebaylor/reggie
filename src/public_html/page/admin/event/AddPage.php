
<tr>
	<td class="required label">Title</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->event['id']
		)) ?>
		
		<?php echo $this->HTML->text(array(
			'name' => 'title',
			'value' => ''
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Visible To</td>
	<td>
		{$this->getCategories()}
	</td>
</tr>
