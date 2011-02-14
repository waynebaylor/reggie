
<div id="content">
	<h3>
		<?php echo $this->title ?>
	</h3>
	
	<table class="admin">
		<tr>
			<th>
				<?php echo implode('</th><th>', $this->info['headings']) ?>
			</th>
			<th></th>
		</tr>
		<?php foreach($this->info['rows'] as $row): ?>
		<tr>
			<td>
				<?php echo implode('</td><td>', $row['data']) ?>
			</td>
			<td>
				<?php if(isset($row['registrationId'])): ?>
					<?php echo $this->HTML->link(array(
						'label' => 'Details',
						'href' => '/admin/registration/Registration',
						'parameters' => array(
							'groupId' => $row['regGroupId'],
							'reportId' => $this->info['reportId']
						)
					)) ?>
					
					<?php echo $this->HTML->link(array(
						'label' => 'Summary',
						'href' => '/admin/registration/Summary',
						'parameters' => array(
							'regGroupId' => $row['regGroupId'],
							'reportId' => $this->info['reportId']
						)
					)) ?>
				<?php endif; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
</div>




