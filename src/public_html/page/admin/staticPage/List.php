
<h3>Event Pages</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Name</th>
			<th>Title</th>
			<th>URL</th>
			<th>Options</th>
		</tr>
		<?php if(empty($this->pages)): ?>
		<tr><td colspan="3">No Pages</td></tr>
		<?php else: ?>
		<?php foreach($this->pages as $page): ?>
		<tr>
			<td>
				<?php echo $page['name'] ?>
			</td>
			<td>
				<?php echo $page['title'] ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => $page['url'],
					'href' => $page['href'],
					'target' => '_blank',
					'title' => 'View page in new window'
				)) ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/staticPage/EditPage',
					'parameters' => array(
						'a' => 'view',
						'id' => $page['id']
					),
					'title' => 'Edit Page Content'
				)) ?>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/staticPage/PageList',
					'parameters' => array(
						'a' => 'removePage',
						'id' => $page['id']
					),
					'title' => 'Delete Page',
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>
</div>
