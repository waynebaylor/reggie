
<h3>
	<?php echo $this->title ?>
</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Status</th>
			<th>Contact Field</th>
			<th>From Address</th>
			<th>Bcc Address</th>
			<th>Registration Types</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->emailTemplates as $template): ?>
		<tr>
			<td>
				<?php echo $template->enabled ?>
			</td>
			<td>
				<?php echo $template->fieldName ?>
			</td>
			<td>
				<?php echo $template->fromAddress ?>
			</td>
			<td>
				<?php echo $template->bcc ?>
			</td>	
			<td>
				<?php echo $template->availableTo ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/emailTemplate/EditEmailTemplate',
					'parameters' => array(
						'a' => 'view',
						'id' => $template->id
					)
				)) ?>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/emailTemplate/EmailTemplates',
					'parameters' => array(
						'a' => 'removeEmailTemplate',
						'id' => $template->id
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>		
		<?php endforeach; ?>
	</table>
</div>


