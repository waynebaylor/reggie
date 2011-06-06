
<h3>Cell Details</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th></th>
			<th>Content</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->selectedCell['content'] as $content): ?>
		<tr>
			<td>
				<?php echo $this->arrows(array(
					'href' => '/admin/badge/EditBadgeTemplate',
					'parameters' => array(
						'cellId' => $this->selectedCell['id']
					),
					'up' => array(
						'a' => 'moveCellContentUp',
						'id' => $content['id']
					),
					'down' => array(
						'a' => 'moveCellContentDown',
						'id' => $content['id']
					)
				)) ?>
			</td>
			<td>
				<?php if(empty($content['contactFieldId'])): ?>
					<?php echo $content['text'] ?>
				<?php else: ?>
					<<?php echo $content['contactFieldName'] ?>>
				<?php endif; ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/badge/EditBadgeTemplate',
					'parameters' => array(
						'a' => 'removeCellContent',
						'id' => $content['id']
					),
					'clas' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
