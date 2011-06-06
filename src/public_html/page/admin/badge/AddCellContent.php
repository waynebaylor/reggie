
<?php if($this->selectedCell['hasBarcode'] === 'T'): ?>
	<!-- TODO: add support for barcode fields -->
<?php else: ?>
<tr>
	<td class="label required">Content</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'cellId',
			'value' => $this->selectedCell['id']
		)) ?>
		<?php echo $this->HTML->radios(array(
			'name' => 'contentType',
			'items' => array(
				array(
					'id' => 'contentType_field',
					'label' => 'Information Field',
					'value' => 'field'
				),
				array(
					'id' => 'contentType_text',
					'label' => 'Text',
					'value' => 'text'
				)
			)	
		)) ?>
	</td>
</tr>
<tr id="content-field" class="content-specifics hide">
	<td class="label required">Field</td>
	<td>
		<?php echo fragment_contactField_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>
<tr id="content-text" class="content-specifics hide">
	<td class="label required">Text</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'text',
			'value' => ''
		)) ?>
	</td>
</tr>
<?php endif; ?>
