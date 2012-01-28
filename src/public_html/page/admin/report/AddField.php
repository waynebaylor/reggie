
<tr>
	<td class="required label">Field</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->event['id']
		)) ?>
		<?php echo $this->HTML->hidden(array(
			'name' => 'reportId',
			'value' => $this->report['id']
		)) ?>
		
		<?php echo fragment_reportField_HTML::select($this->event) ?>
	</td>
</tr>




