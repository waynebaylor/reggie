
<script type="text/javascript">
	dojo.require("hhreg.list");
</script>

<h3>Fields</h3>

<div class="fragment-list">
	<table class="admin">
		<tr>
			<th></th>
			<th>Name</th>
			<th>Options</th>
		</tr>
		
		<?php if($this->report['showDateRegistered'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Date Registered</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'date_registered',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if($this->report['showDateCancelled'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Date Cancelled</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'date_cancelled',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if($this->report['showCategory'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Category</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'category',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if($this->report['showRegType'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Registration Type</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'registration_type',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php foreach($this->report['fields'] as $field): ?>
		<tr>
			<td>
				<?php echo $this->arrows(array(
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'reportId' => $this->report['id']
					),
					'up' => array(
						'a' => 'moveFieldUp',
						'id' => $field['id']
					),
					'down' => array(
						'a' => 'moveFieldDown',
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
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => $field['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
		
		<?php if($this->report['showTotalCost'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Total Cost</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'total_cost',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if($this->report['showTotalPaid'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Total Paid</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'total_paid',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
		
		<?php if($this->report['showRemainingBalance'] === 'T'): ?>
		<tr>
			<td></td>
			<td>Remaining Balance</td>
			<td>
				<?php echo $this->HTML->link(array(
					'label' => 'Remove',
					'href' => '/admin/report/EditReport',
					'parameters' => array(
						'a' => 'removeField',
						'id' => 'remaining_balance',
						'reportId' => $this->report['id']
					),
					'class' => 'remove'
				)) ?>
			</td>
		</tr>
		<?php endif; ?>
	</table>
</div>




