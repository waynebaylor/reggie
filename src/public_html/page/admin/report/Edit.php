
<tr>
	<td class="required label">Name</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->report['id']
		)) ?>
		
		<?php echo $this->HTML->text(array(
			'name' => 'name',
			'value' => $this->escapeHtml($this->report['name']),
			'maxlength' => 255
		)) ?>
	</td>
</tr>
<tr>
	<td class="required label">Report Type</td>
	<td>
		<?php echo $this->HTML->radios(array(
			'name' => 'type',
			'value' => $this->escapeHtml($this->report['type']),
			'items' => array(
				array(
					'label' => 'Standard',
					'value' => model_Report::$STANDARD
				),
				array(
					'label' => 'All Registrations To Date',
					'value' => model_Report::$ALL_REG_TO_DATE
				),
				array(
					'label' => 'Option Counts',
					'value' => model_Report::$OPTION_COUNTS
				),
				array(
					'label' => 'Payments To Date',
					'value' => model_Report::$PAYMENTS_TO_DATE
				),
				array(
					'label' => 'Registration Type Breakdown',
					'value' => model_Report::$REG_TYPE_BREAKDOWN
				),
				array(
					'label' => 'Rosters',
					'value' => model_Report::$OPTION_ROSTER
				)
			)
		)) ?>
	</td>
</tr>



