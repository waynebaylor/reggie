<tr>
	<td colspan="2">
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->eventId
		)) ?>
		<p>Select template(s) to use for this print job.</p>
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
	</td>
</tr>