
<tr>
	<td class="label required">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => '',
			'size' => 30,
			'maxlength' => 255
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Registration Types</td>
	<td>
		<?php echo fragment_regType_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>


