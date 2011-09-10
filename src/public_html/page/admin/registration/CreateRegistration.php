
<tr>
	<td class="label required">Category</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		
		<?php echo fragment_category_HTML::radios(array('name' => 'categoryId')) ?>
	</td>
</tr>






