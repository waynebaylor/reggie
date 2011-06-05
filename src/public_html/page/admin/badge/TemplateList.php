
<h3>Badge Templates</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Name</th>
			<th>Registration Types</th>
			<th>Options</th>
		</tr>
		<?php if(empty($this->templates)): ?>
		<tr><td colspan="3">No Templates</td></tr>
		<?php else: ?>
		<?php foreach($this->templates as $template): ?>
		<tr>
			<td>
				<?php echo $template['name'] ?>
			</td>
			<td>
				<?php echo page_admin_badge_Helper::getRegTypes($template) ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/badge/EditBadgeTemplate',
					'parameters' => array(
						'id' => $template['id']
					),
					'title' => 'Edit Badge Template'
				)) ?>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/badge/BadgeTemplates',
					'parameters' => array(
						'a' => 'removeTemplate',
						'id' => $template['id']
					),
					'title' => 'Delete Badge Template',
					'class' => 'remove'
				)) ?>				
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>
</div>




