
<tr>
	<td class="label required">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->template['id']
		)) ?>
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => $this->escapeHtml($this->template['name']),
			'size' => 30,
			'maxlength' => 255
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Template Type</td>
	<td>
		<?php echo fragment_badge_HTML::selectByEventId($this->eventId, array('value' => $this->template['type'])) ?>
	</td>
</tr>
<tr>
	<td class="label required">Registration Types</td>
	<td>
		<?php echo fragment_regType_HTML::selectByEventId($this->eventId, array('value' => $this->appliesToRegTypeIds)) ?>
	</td>
</tr>