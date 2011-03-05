
<h3>Pages</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th></th>
			<th>Title</th>
			<th>Visible To</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->event['pages'] as $page): ?>
		<tr>
			<td>
				<?php echo $this->arrows(array(
					'href' => '/admin/event/EditEvent',
					'parameters' => array(
						'eventId' => $this->event['id']
					),
					'up' => array(
						'action' => 'movePageUp',
						'id' => $page['id']
					),
					'down' => array(
						'action' => 'movePageDown',
						'id' => $page['id']
					)
				)) ?>
			</td>
			<td>
				<?php echo $page['title'] ?>
			</td>
			<td>
				<?php foreach($page['visibleTo'] as $category): ?>
				<div>
					<?php echo $category['displayName'] ?>
				</div>
				<?php endforeach; ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/page/Page',
					'parameters' => array(
						'action' => 'view',
						'id' => $page['id'],
						'eventId' => $this->event['id']
					)
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/event/EditEvent',
					'parameters' => array(
						'action' => 'removePage',
						'id' => $page['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>
