
<h3>Events</h3>

<div class="fragment-list">
	<table class="admin">
		<?php if(empty($this->events)): ?>
		<tr><td>No Events</td></tr>
		<?php else: ?>
		<?php foreach($this->events as $eventInfo): ?>
		<tr>
			<td>
				<?php echo "{$eventInfo['event']['displayName']} ({$eventInfo['event']['code']})" ?>
			</td>
			<td>
				<?php echo ucfirst($eventInfo['status']) ?>
			</td>
			<td>
				<?php foreach(model_Category::values() as $category): ?>
				<?php $pages = model_EventPage::getVisiblePages($eventInfo['event'], $category);
					  if(!empty($pages)): ?>
				<?php echo $this->HTML->link(array(
					'label' => $category['displayName'],
					'href' => "/event/{$eventInfo['event']['code']}/".model_Category::code($category),
					'title' => 'As seen by '.$category['displayName'],
					'target' => '_blank'
				)) ?>
				<?php endif; ?>
				<?php endforeach; ?>
			</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Edit',
					'href' => '/admin/event/EditEvent',
					'parameters' => array(
						'action' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Edit Event'
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Reports',
					'href' => '/admin/report/Report',
					'parameters' => array(
						'action' => 'eventReports',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Event Reports'
				)) ?>
				
				<?php echo $this->HTML->link(array(
					'label' => 'Files',
					'href' => '/admin/fileUpload/FileUpload',
					'parameters' => array(
						'action' => 'view',
						'id' => $eventInfo['event']['id']
					),
					'title' => 'Event Files'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
	</table>	
</div>

