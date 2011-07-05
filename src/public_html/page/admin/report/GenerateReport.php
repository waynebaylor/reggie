
<script type="text/javascript">
	dojo.require("hhreg.dialog");
	dojo.require("hhreg.xhrTableForm");
	dojo.require("hhreg.util");
	dojo.require("dijit.form.Button");

	dojo.addOnLoad(function() {
		// create registration link and form dialog.
		var createRegLink = dojo.byId("create-reg-link");
		if(createRegLink) {
			var createRegContent = dojo.byId("create-reg-content");
			var createRegForm = dojo.query("form", createRegContent)[0];
			var redirectUrl = dojo.byId("create-reg-redirect").value;
			
			var createRegDialog = hhreg.dialog.create({
				title: "Create New Registration",
				trigger: createRegLink,
				content: createRegContent,
				onClose: function() {
					hhreg.xhrTableForm.hideIcons(createRegForm)
				}
			});
			
			hhreg.xhrTableForm.bind(createRegForm, function() { 
				createRegDialog.hide();
				document.location = hhreg.util.contextUrl(redirectUrl);
			});
		}

		// search link and form dialog.
		var searchLink = dojo.byId("search-form-link");
		if(searchLink) {
			var searchContent = dojo.byId("search-form-content");
			var searchForm = dojo.query("form", searchContent)[0];
	
			var searchDialog = hhreg.dialog.create({
				title: "Search Report",
				trigger: searchLink,
				content: searchContent
			});		
	
			var plainSearchButton = dojo.query("input[type=button]", searchContent)[0];
			var searchButton = new dijit.form.Button({
				label: plainSearchButton.value,
				onClick: function() {
					searchForm.submit();
				}
			}, plainSearchButton);
			searchButton.startup();
	
			dojo.connect(searchForm, "onkeypress", function(event) {
				if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
					dojo.stopEvent(event);
					searchForm.submit();
				}
			});
		}
	});
</script>

<div id="content">
	<h3>
		<?php echo $this->title ?>
	</h3>
	
	<?php if(!empty($this->isSearch)): ?>
	<div>
		<strong>Search Results For:</strong> 
		'<?php echo $this->searchField ?>' 
		<strong>containing</strong>
		'<?php echo $this->searchTerm ?>'.
		<br>
		<em><?php echo count($this->info['rows']) ?> records found.</em>
	</div>
	<?php endif; ?>

	<div class="sub-divider"></div>
	
	<?php if(ArrayUtil::getValue($this->info, 'showSearchLink', true)): ?>
	<span id="search-form-link" class="link">Search</span>
	<div id="search-form-content" class="hide">
		<?php echo $this->tableForm(         
			'/admin/report/GenerateReport',
			'search',
			$this->getFileContents('page_admin_report_SearchForm'),
			'Search'
		) ?>
	</div>
	&nbsp;
	<?php endif; ?>
	
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
	<?php endif; ?>
	
	<div class="sub-divider"></div>
				
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




