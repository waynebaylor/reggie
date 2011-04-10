
<tr>
	<td colspan="2">
		The new registration will show up as a blank row at the bottom of the list. 
		<br/>
		Click the 'Details' link to select a registration type
		<br/>
		and complete the registrant's information.
		
		<div class="sub-divider"></div>
	</td>
</tr>
<tr>
	<td class="label">Category</td>
	<td>
		<?php echo $this->HTML->hidden(array(
			'name' => 'reportId',
			'value' => $this->info['reportId']
		)) ?>
		
		<?php echo $this->HTML->hidden(array(
			'name' => 'eventId',
			'value' => $this->info['eventId']
		)) ?>
		
		<?php echo $this->HTML->hidden(array(
			'id' => 'create-reg-redirect',
			'value' => "/admin/report/GenerateReport?id={$this->info['reportId']}"
		)) ?>
		
		<?php echo fragment_category_HTML::radios(array('name' => 'categoryId')) ?>
	</td>
</tr>



