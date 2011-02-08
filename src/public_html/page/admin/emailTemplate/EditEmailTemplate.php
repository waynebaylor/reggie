
<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
</script>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<?php echo $this->xhrTableForm(
			'/admin/emailTemplate/EditEmailTemplate',
			'saveEmailTemplate',
			$this->getFileContents('page_admin_emailTemplate_Edit')
		) ?>
	</div>
	
	<div class="divider"></div>
	
	<div id="email-test">
		<form method="post" action="<?php echo $this->contextUrl('/admin/emailTemplate/EditEmailTemplate') ?>">
			<?php echo $this->HTML->hidden(array(
				'name' => 'id',
				'value' => $this->emailTemplate['id']
			)) ?>
			<?php echo $this->HTML->hidden(array(
				'name' => 'a',
				'value' => 'sendTestEmail'
			)) ?>
			
			<p>
				Send Test Email <?php echo $this->HTML->text(array(
					'name' => 'toAddress',
					'value' => '',
					'size' => 30
				)) ?>
				
				<input type="submit" class="button" value="Send">
			</p>
		</form>
	</div>
</div>

