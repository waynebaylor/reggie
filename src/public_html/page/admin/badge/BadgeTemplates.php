
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	dojo.require("dijit.form.Button");
	dojo.require("hhreg.dialog");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-templates").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});

		// print badges link and dialog.
		var printDialog = hhreg.dialog.create({
			title: 'Print Badges',
			trigger: dojo.byId("print-badges-link"),
			content: dojo.byId("print-badges-form")
		});

		var printBadgesForm = dojo.query("form", printDialog.domNode)[0];

		var plainButton = dojo.query("input[type=button]", printDialog.domNode)[0];
		new dijit.form.Button({
			label: plainButton.value,
			onClick: function() {
				printBadgesForm.submit();
			}
		}, plainButton).startup();

		dojo.connect(printBadgesForm, "onkeypress", function(event) {
			if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
				dojo.stopEvent(event);
				printBadgesForm.submit();
			}
		});
	});
</script>

<div id="content">
	<div class="fragment-templates">
		<div>
			<?php echo $this->getFileContents('page_admin_badge_TemplateList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Badge Template',
				'/admin/badge/BadgeTemplates',
				'addTemplate',
				$this->getFileContents('page_admin_badge_AddTemplate')
			) ?>
		</div>
	</div>
</div>


