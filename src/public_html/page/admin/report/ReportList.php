
<script type="text/javascript">
	dojo.require("hhreg.list");
</script>

<h3>Reports</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th>Name</th>
			<th>Options</th>
		</tr>
		<?php foreach($this->event['reports'] as $report): ?>
			<tr>
				<td>
					<?php echo $this->HTML->link(array(
						'label' => $report['name'],
						'href' => '/admin/report/GenerateReport',
						'title' => 'Generate Report',
						'parameters' => array(
							'a' => 'view',
							'id' => $report['id']
						)
					)) ?>
					(<?php echo $this->HTML->link(array(
						'label' => 'csv',
						'title' => 'Download report in CSV format',
						'href' => '/admin/report/GenerateReport',
						'parameters' => array(
							'a' => 'csv',
							'id' => $report['id']
						)
					))?>)
				</td>
				<td>
					<?php echo $this->HTML->link(array(
						'label' => 'Edit',
						'href' => '/admin/report/EditReport',
						'parameters' => array(
							'action' => 'view',
							'id' => $report['id'],
							'eventId' => $this->event['id']
						)
					))?>
					
					<?php if(model_Report::hasSearch($report)): ?>
					<span class="search-form-link">
						<span class="link">Search</span>
						<?php echo $this->HTML->hidden(array(
							'name' => 'reportId',
							'value' => $report['id']
						)) ?>
					</span>
					<?php else: ?>
					<span style="visibility:hidden;">Search</span>
					<?php endif; ?>
					
					<?php echo $this->HTML->link(array(
						'label' => 'Remove',
						'href' => '/admin/report/Reports',
						'parameters' => array(
							'action' => 'removeReport',
							'id' => $report['id']
						),
						'class' => 'remove'
					))?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
</div>

