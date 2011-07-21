<tr>
	<td colspan="2">
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<p>Select template(s) to use for this print job</p>
		<table class="admin">
			<tr>
				<th></th>
				<th>Name</th>
				<th>Registration Types</th>
			</tr>
			<?php foreach($this->templates as $template): ?>
			<tr>
				<td>
					<?php echo $this->HTML->checkbox(array(
						'id' => 'templateIds_'.$template['id'],
						'name' => 'templateIds[]',
						'value' => $template['id']
					)) ?>
				</td>
				<td>
					<label for="templateIds_<?php echo $template['id'] ?>">
						<?php echo $template['name'] ?>
					</label>
				</td>
				<td>
					<?php echo page_admin_badge_Helper::getRegTypes($template) ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</table>
		
		<div class="sub-divider"></div>
	</td>
</tr>
<tr>
	<td>Sort By</td>
	<td>
		<?php echo page_admin_badge_Helper::selectFields($this->event, 'sortByFieldId') ?>
		
		<div class="sub-divider"></div>
	</td>
</tr>
<tr>
	<td class="label">Date Range</td>
	<td>
		<?php echo $this->HTML->text(array(
			'name' => 'startDate',
			'value' => '',
			'size' => 8
		)) ?>
		-		
		<?php echo $this->HTML->text(array(
			'name' => 'endDate',
			'value' => '',	
			'size' => 8
		)) ?>
		&nbsp; <span style="color:gray;">yyyy-mm-dd hh:mm</span>
	</td>
</tr>

