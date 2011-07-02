
<tr>
	<td class="label required">Content</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'badgeTemplateId',
			'value' => $this->template['id']
		)) ?>
		<?php echo $this->HTML->radios(array(
			'name' => 'contentType',
			'items' => array(
				array(
					'id' => 'contentType_field',
					'label' => 'Field',
					'value' => 'field'
				),
				array(
					'id' => 'contentType_text',
					'label' => 'Text',
					'value' => 'text'
				),
				array(
					'id' => 'contentType_barcode',
					'label' => 'Barcode',
					'value' => 'barcode'
				)
			)	
		)) ?>
	</td>
</tr>
<tr id="content-field" class="content-specifics hide">
	<td class="label required">Field</td>
	<td>
		<?php echo page_admin_badge_Helper::selectFields($this->event) ?>
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
<tr id="content-barcode" class="content-specifics hide">
	<td></td>
	<td>
		You can add information to the barcode 
		after it is created.
	</td>
</tr>