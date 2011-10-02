
<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
</script>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<?php echo $this->xhrTableForm(array(
			'url' => '/admin/emailTemplate/EditEmailTemplate',
			'action' => 'saveEmailTemplate',
			'rows' => $this->getFileContents('page_admin_emailTemplate_Edit')
		)) ?>
	</div>
	
	<div class="divider"></div>
	
	<div id="email-test" class="fragment-edit">
		<h3>Send Test Email</h3>
		
		<?php $sendTestRows = <<<_
			<tr>
				<td class="required label">To Address</td>
				<td>
					{$this->HTML->hidden(array('name' => 'eventId', 'value' => $this->eventId))}
					{$this->HTML->hidden(array('name' => 'id', 'value' => $this->emailTemplate['id']))}
					{$this->HTML->text(array('name' => 'toAddress',	'value' => '', 'size' => 30))}
				</td>
			</tr>
_;
		?>
		
		<?php echo $this->xhrTableForm(array(
			'url' => '/admin/emailTemplate/EditEmailTemplate',
			'action' => 'sendTestEmail',
			'rows' => $sendTestRows,
			'buttonText' => 'Send',
			'errorText' => 'There was a problem sending the email. Please try again.'
		)) ?>
	</div>
</div>

