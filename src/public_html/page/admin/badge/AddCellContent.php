
<?php if($this->selectedCell['hasBarcode'] === 'T'): ?>
<tr>
	<td class="label required">Field</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'cellId',
			'value' => $this->selectedCell['id']
		)) ?>
		<?php echo fragment_contactField_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>
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
					'id' => 'contentType_field_cell_content',
					'label' => 'Information Field',
					'value' => 'field'
				),
				array(
					'id' => 'contentType_text_cell_content',
					'label' => 'Text',
					'value' => 'text'
				)
			)	
		)) ?>
	</td>
</tr>
<tr id="content-field-cell-content" class="content-specifics hide">
	<td class="label required">Field</td>
	<td>
		<?php echo fragment_contactField_HTML::selectByEventId($this->eventId) ?>
	</td>
</tr>
<tr id="content-text-cell-content" class="content-specifics hide">
	<td class="label required">Text</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'text',
			'value' => ''
		)) ?>
	</td>
</tr>
<?php endif; ?>
