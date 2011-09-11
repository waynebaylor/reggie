
<tr>
	<td class="label required">Registration Type</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		
		<?php echo fragment_regType_HTML::selectByEventId($this->eventId, array(
			'name' => 'regTypeId', 'multiple' => false, 'size' => 1
		)) ?>
	</td>
</tr>






