
<style type="text/css">
#template-layout h3 {
	margin-top: 10px;
}

#template-layout {
	width: 100%;
	border-collapse: collapse;
}

#template-layout td.layout {
	border: 1px solid #999;
	padding: 0  5px 5px;
	vertical-align: top;
	background-color: #eee;
}

#badge-canvas {
	height: 3in;
	width: 4in;
	border: 1px solid black;
	margin: 0 auto 10px;
	background-color: white;
}

#template-layout td#badge-preview {
	background-color: #ccc;
}
</style>

<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-cells").forEach(function(item) {
			hhreg.xhrAddList.bind(item);
		});

		dojo.query(".fragment-add input[name=contentType]").forEach(function(item) {
			dojo.connect(dojo.byId("contentType_field"), "onclick", function() {
				dojo.query(".fragment-add .content-specifics").addClass("hide");
				dojo.removeClass(dojo.byId("content-field"), "hide");
			});

			dojo.connect(dojo.byId("contentType_text"), "onclick", function() {
				dojo.query(".fragment-add .content-specifics").addClass("hide");
				dojo.removeClass(dojo.byId("content-text"), "hide");
			});

			dojo.connect(dojo.byId("contentType_barcode"), "onclick", function() {
				dojo.query(".fragment-add .content-specifics").addClass("hide");
				dojo.removeClass(dojo.byId("content-barcode"), "hide");
			});
		});
	});
</script>

<div id="content">
	<div id="edit-badge-workarea">
		<h3><?php echo $this->title ?></h3>
		
		<table id="template-layout">
			<tr>
				<td class="layout" rowspan="2">
					<div class="fragment-cells">
						<div>
							<?php echo $this->getFileContents('page_admin_badge_TemplateCells') ?>
						</div>
						
						<div class="sub-divider"></div>
					
						<div class="fragment-add">
							<?php echo $this->xhrAddForm(
								'Add Cell',
								'/admin/badge/EditBadgeTemplate',
								'addBadgeCell',
								$this->getFileContents('page_admin_badge_AddCell')
							) ?>
						</div>
					</div>
				</td>
				<td id="badge-preview" class="layout">
					<h3>Preview</h3>
					<div id="badge-canvas"></div>
				</td>
				<td class="layout" rowspan="2">
					<h3>Cell Details</h3>
				</td>
			</tr>
			<tr>
				<td class="layout">
					<h3>Current Cell</h3>
				</td>
			</tr>
		</table>		
	</div>
</div>