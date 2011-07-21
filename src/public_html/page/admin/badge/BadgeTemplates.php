
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	dojo.require("dijit.form.Button");
	dojo.require("hhreg.dialog");

	var sendBatchRequest = function(batch, dialog) {
		return setTimeout(function() {
			dojo.xhrGet({
				url: hhreg.util.contextUrl("/admin/badge/PrintBadge")+"?"+dojo.objectToQuery(batch),
				handleAs: "text",
				handle: function(response) { 
					var responseNode = dojo.place("<div>"+response+"</div>", dojo.body());
					var xhrResponse = dojo.byId("xhr-response");

					if(xhrResponse && xhrResponse.name === "success") {
						dojo.byId("batches-completed").innerHTML = parseInt(dojo.byId("batches-completed").innerHTML) + 1;
					}
					else {
						dialog.set("content", "Error");
					}

					dojo.query(responseNode).orphan();
				},
				timeout: 1*60*1000 // millisecs
			});
		}, 60*1000*batch.batchNumber);
	};
	
	var processBadgePrinting = function(form, dialog) {
		dojo.xhrPost({
			form: form,
			handleAs: "json",
			load: function(response) { 
				var contentString = '<div style="margin:30px;"><div>Printing <strong>${totalBadges}</strong> badges in <strong>${batchCount}</strong> batches.</div><div style="text-align:center;margin-top:20px;">Completed <span id="batches-completed" style="font-size:18pt; color:#579; font-weight:bold;">0</span> of <span style="font-size:18pt; color:#579; font-weight:bold;">${batchCount}</span></div></div>';
				var content = dojo.place(
					dojo.string.substitute(contentString, {
						totalBadges: response.totalBadges, 
						batchCount: response.batches.length
					}),
					dojo.body()
				);

				var batchCountDialog = hhreg.dialog.create({
					title: "Printing Badges",
					content: content
				});

				dialog.hide();
				batchCountDialog.show();
				
				dojo.forEach(response.batches, function(batch) {
					var handle = sendBatchRequest(batch, batchCountDialog);
					
					dojo.connect(batchCountDialog, "hide", function() {
						clearTimeout(handle);
					});
				});
			},
			error: function(response) {
				dialog.set('content', '<div style="padding:30px;">Error</div>');
			}
		});
	};
	
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
				processBadgePrinting(printBadgesForm, printDialog);
			}
		}, plainButton).startup();

		dojo.connect(printBadgesForm, "onkeypress", function(event) {
			if(event.keyCode === dojo.keys.ENTER && event.target.tagName.toLowerCase() !== 'textarea') {
				dojo.stopEvent(event);
				processBadgePrinting(printBadgesForm, printDialog);
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


