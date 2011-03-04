
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
		<div>
			<?php echo fragment_category_HTML::checkboxes(array(
				'name' => 'categoryIds[]',
			)) ?>
		</div>
	</td>
</tr>
