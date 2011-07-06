
<h3>Badge Templates</h3>

<span id="print-badges-link" class="link">Print Badges</span>
<div id="print-badges-form" class="hide">
	<?php echo $this->tableForm(
		'/admin/badge/PrintBadge',
		'allBadges',
		$this->getFileContents('page_admin_badge_PrintBadgeForm'),
		'Print Badges'
	) ?>
</div>

<div class="sub-divider"></div>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Name</th>
			<th>Type</th>
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
				<?php $view_templateType = model_BadgeTemplateType::valueOf($template['type']); echo $view_templateType['name'] ?>
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
					'label' => 'Copy',
					'href' => '/admin/badge/BadgeTemplates',
					'parameters' => array(
						'a' => 'copyTemplate',
						'id' => $template['id']
					),
					'title' => 'Copy Badge Template'
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




