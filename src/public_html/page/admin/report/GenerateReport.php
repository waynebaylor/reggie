
<script type="text/javascript">
	dojo.require("hhreg.dialog");
	dojo.require("hhreg.xhrTableForm");
	dojo.require("hhreg.util");

	dojo.addOnLoad(function() {
		var triggerLink = dojo.byId("create-reg-link");
		var content = dojo.byId("create-reg-content");
		var form = dojo.query("form", content)[0];
		var redirectUrl = dojo.byId("create-reg-redirect").value;
		
		var dialog = hhreg.dialog.create({
			title: "Create New Registration",
			trigger: triggerLink,
			content: content,
			onClose: function() {
				hhreg.xhrTableForm.hideIcons(form)
			}
		});
		
		hhreg.xhrTableForm.bind(form, function() { 
			dialog.hide();
			document.location = hhreg.util.contextUrl(redirectUrl);
		});
	});
</script>

<div id="content">
	<h3>
		<?php echo $this->title ?>
	</h3>
	
	<?php if(ArrayUtil::getValue($this->info, 'showCreateRegLink', true)): ?>
	<span id="create-reg-link" class="link">Create New Registration</span>
	<div id="create-reg-content" class="hide">
		<?php echo $this->xhrTableForm(
			'/admin/registration/Registration',
			'createNewRegistration',
			$this->getFileContents('page_admin_report_AddRegistrationForm'),
			'Continue'
		) ?>
	</div>
	
	<div class="sub-divider"></div>
	<?php endif; ?>
				
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




