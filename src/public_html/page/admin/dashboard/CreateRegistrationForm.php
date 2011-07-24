
<tr>
	<td colspan="2">
		Please choose the new registrant's category. <br>
		You will be able to select their registration <br>
		type and enter their information on the next page.
		
		<div class="sub-divider"></div>
	</td> 
</tr>
<tr>
	<td class="label required">Category</td>
	<td>
		<?php // the eventId is dynamically added to the form by the js. ?>
		<?php echo fragment_category_HTML::radios(array('name' => 'categoryId')) ?>
	</td>
</tr>



