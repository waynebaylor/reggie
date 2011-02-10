

<tr>
	<td class="label required">Status</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->emailTemplate['id']
		)) ?>
		<?php echo $this->HTML->radios(array(
			'name' => 'enabled',
			'value' => $this->emailTemplate['enabled'],
			'items' => array(
				array(
					'label' => 'Enabled',
					'value' => 'T'
				),
				array(
					'label' => 'Disabled',
					'value' => 'F'
				)
			)
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Contact Field</td>
	<td>
		<?php echo fragment_contactField_HTML::selectByEventId(
			$this->emailTemplate['eventId'], 
			array('value' => $this->emailTemplate['contactFieldId'])) 
		?>
	</td>
</tr>
<tr>
	<td class="label required">From Address</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'fromAddress',
			'value' => $this->escapeHtml($this->emailTemplate['fromAddress']),
			'size' => 30
		)) ?>
	</td>	
</tr>
<tr>
	<td class="label">Bcc Address</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'bcc',
			'value' => $this->escapeHtml($this->emailTemplate['bcc']),
			'size' => 30
		)) ?>
	</td>
</tr>
<tr>
	<td class="label required">Registration Types</td>
	<td>
		<?php echo fragment_regType_HTML::selectByEventId(
			$this->emailTemplate['eventId'], 
			array('value' => $this->emailTemplate['regTypeIds'])) 
		?>
	</td>
</tr>
<tr>
	<td class="label">Subject</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'subject',
			'value' => $this->escapeHtml($this->emailTemplate['subject']),
			'size' => 50
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Before Summary</td>
	<td>
		<?php echo $this->HTML->textarea(array(
			'name' => 'header',
			'value' => $this->escapeHtml($this->emailTemplate['header']),
			'rows' => 10,
			'cols' => 75
		)) ?>
	</td>
</tr>
<tr>
	<td></td>
	<td>[Registration Summary]</td>
</tr>
<tr>
	<td class="label">After Summary</td>
	<td>
		<?php echo $this->HTML->textarea(array(
			'name' => 'footer',
			'value' => $this->escapeHtml($this->emailTemplate['footer']),
			'rows' => 10,
			'cols' => 75
		)) ?>
	</td>
</tr>



