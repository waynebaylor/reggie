
<tr>
	<td class="label">
		Search for
	</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'reportId',
			'value' => $this->searchFormInfo['reportId']
		)) ?>
		
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->searchFormInfo['eventId']
		)) ?>
		
		<?php echo $this->HTML->text(array(
			'name' => 'term',
			'value' => '',
			'size' => 20
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">
		Column
	</td>
	<td>
		<?php echo fragment_reportField_HTML::select($this->searchFormInfo['event'], false) ?>
	</td>
</tr>




