
<h3>
	<?php echo $this->title ?>
</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th></th>
			<th>Field</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->report['fields'] as $field): ?>
		<tr>
			<td>
				<?php $this->arrows(array(
					'href' => '/admin/report/EditPaymentsToDate',
					'parameters' => array(),
					'up' => array(
						'action' => 'moveFieldUp',
						'id' => $field['id']
					),
					'down' => array(
						'action' => 'moveFieldDown',
						'id' => $field['id']
					)
				)) ?>
			</td>
			<td>
				<?php echo $field['displayName'] ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditPaymentsToDate',
					'parameters' => array(
						'a' => 'removeField',
						'id' => $field['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>