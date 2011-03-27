
<tr>
	<td colspan="2">
		<?php echo $this->HTML->hidden(array(
			'name' => 'reportId',
			'value' => $this->info['reportId']
		)) ?>
		
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->info['eventId']
		)) ?>
		
		Look for
		<?php echo $this->HTML->text(array(
			'name' => 'term',
			'value' => ''
		)) ?>
		in
		<?php echo $this->HTML->select(array(
			'name' => 'field',
			'items' => array()
		)) ?>
	</td>
</tr>




