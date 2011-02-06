
<tr>
	<td class="label required">Status</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<?php echo $this->HTML->radios(array(
			'name' => 'enabled',
			'value' => 'true',
			'items' => array(
				array(
					'label' => 'Enabled',
					'value' => 'true'
				),
				array(
					'label' => 'Disabled',
					'value' => 'false'
				)
			)
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Contact Field</td>
	<td>
		<?php echo fragment_contactField_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>
<tr>
	<td class="label required">From Address</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'fromAddress',
			'value' => '',
			'size' => 30
		)) ?>
	</td>	
</tr>
<tr>
	<td class="label">Bcc Address</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'bcc',
			'value' => '',
			'size' => 30
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Registration Types</td>
	<td>
		<?php echo fragment_regType_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>
