
<tr>
	<td class="required label">Code</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'id',
			'value' => $this->event['id']
		)) ?>
		
		<?php echo $this->HTML->hidden(array(
			'name' => 'paymentInstructions',
			'value' => $this->escapeHtml($this->event['paymentInstructions'])
		)) ?>
		
		<?php echo $this->HTML->text(array(
			'name' => 'code',
			'value' => $this->escapeHtml($this->event['code']),
			'maxlength' => '255'
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Title</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'displayName',
			'value' => $this->escapeHtml($this->event['displayName']),
			'size' => '50',
			'maxlength' => '255'
		)) ?>
	</td>
</tr>
<tr>
	<td class="required label">Registration Open</td>
	<td>
		<?php echo $this->HTML->calendar(array(
			'name' => 'regOpen',
			'value' => $this->escapeHtml($this->event['regOpen']),
			'size' => '16',
			'maxlength' => '16'
		)) ?>
	</td>
</tr>
<tr>
	<td class="required label">Registration Closed</td>
	<td>
		<?php echo $this->HTML->calendar(array(
			'name' => 'regClosed',
			'value' => $this->escapeHtml($this->event['regClosed']),
			'size' => '16',
			'maxlength' => '16'
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Capacity</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'capacity',
			'value' => $this->escapeHtml($this->event['capacity']),
			'size' => 5
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Confirmation Page Text</td>
	<td>
		<?php echo $this->HTML->textarea(array(
			'name' => 'confirmationText',
			'value' => $this->escapeHtml($this->event['confirmationText']),
			'rows' => 10,
			'cols' => 75
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Registration Closed Text</td>
	<td class="admin_td">
		<?php echo $this->HTML->textarea(array(
			'name' => 'regClosedText',
			'value' => $this->escapeHtml($this->event['regClosedText']),
			'rows' => 10,
			'cols' => 75
		)) ?>
	</td>
</tr>
<tr>
	<td class="label">Cancellation Policy</td>
	<td class="admin_td">
		<?php echo $this->HTML->textarea(array(
			'name' => 'cancellationPolicy',
			'value' => $this->escapeHtml($this->event['cancellationPolicy']),
			'rows' => 10,
			'cols' => 75
		)) ?>
	</td>
</tr>