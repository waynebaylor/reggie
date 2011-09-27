
<script type="text/javascript">
	dojo.require("hhreg.util");
	dojo.require("hhreg.xhrEditForm");
	dojo.require("hhreg.calendar");
	dojo.require("hhreg.xhrAddList");
	dojo.require("dijit.layout.TabContainer");
    dojo.require("dojox.layout.ContentPane");

	dojo.addOnLoad(function() {
		var eventId = <?php echo $this->event['id'] ?>;
		
		dojo.query(".fragment-pages").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});

		//////////////////////////////

		var tabContainer = new dijit.layout.TabContainer({
			style: "width: 100%; height: 100%;",
			doLayout: false
		}, dojo.byId("edit-event-tabs"));


		tabContainer.addChild(new dojox.layout.ContentPane({
			title: "General",
			content: dojo.byId("edit-event-general")
		}));
		
		tabContainer.addChild(new dojox.layout.ContentPane({
			title: "Appearance",
			href: hhreg.util.contextUrl("/admin/event/EditAppearance?")+dojo.objectToQuery({"eventId": eventId})
		}));

		tabContainer.addChild(new dojox.layout.ContentPane({
			title: "Payment Options",
			href: hhreg.util.contextUrl("/admin/event/EditPaymentOptions?")+dojo.objectToQuery({"eventId": eventId})
		}));

		tabContainer.addChild(new dojox.layout.ContentPane({
			title: "Email Templates",
			href: hhreg.util.contextUrl("/admin/emailTemplate/EmailTemplates?")+dojo.objectToQuery({"eventId": eventId})
		}));

		tabContainer.addChild(new dojox.layout.ContentPane({
			title: "Group Registration",
			href: hhreg.util.contextUrl("/admin/event/EditGroupRegistration?")+dojo.objectToQuery({"eventId": eventId})
		}));
		
		tabContainer.startup();
	});
</script>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<div id="edit-event-tabs"></div>
		
		<div id="edit-event-general">
			<?php echo $this->xhrTableForm(
				'/admin/event/EditEvent', 
				'saveEvent', 
				$this->getFileContents('page_admin_event_Edit')
			) ?>
		</div>
	</div>
	
	<div class="divider"></div>
	
	<div class="fragment-pages">
		<div>
			<?php echo $this->getFileContents('page_admin_event_PageList') ?>
		</div>
		
		<div class="sub-divider"></div>
		
		<div class="fragment-add">
			<?php echo $this->xhrAddForm(
				'Add Page', 
				'/admin/event/EditEvent', 
				'addPage', 
				$this->getFileContents('page_admin_event_AddPage')
			) ?>
		</div>
	</div>
</div>

