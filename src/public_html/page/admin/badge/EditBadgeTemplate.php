
<style type="text/css">
#template-layout h3 {
	margin-top: 10px;
}

#template-layout {
	width: 100%;
	border-collapse: collapse;
}

#template-layout td.layout {
	border: 1px solid #555;
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

#template-layout .badge-cell {
	padding: 5px;
	cursor: pointer;
}

#template-layout .badge-cell-selected {
	background-color: #b7dcff;
	border: 1px solid #769DC0;
}

#template-layout .badge-cell:hover {
	background-color: #dddee3;
}
</style>

<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	dojo.require("hhreg.xhrTableForm");

	dojo.addOnLoad(function() {
		dojo.query(".fragment-edit-general form").forEach(function(item) {
			hhreg.xhrTableForm.bind(item);
		});
		
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

		dojo.query(".badge-cell").forEach(function(item) {
			
		});
	});
</script>

<div id="content">
	<div class="fragment-edit-general">
		<h3><?php echo $this->title ?></h3>
		
		<?php echo $this->xhrTableForm(
			'/admin/badge/EditBadgeTemplate',
			'saveTemplate',
			$this->getFileContents('page_admin_badge_EditTemplateInfo')
		) ?>
	</div>
	
	<div class="divider"></div>
	
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
				<div>
					<?php echo $this->getFileContents('page_admin_badge_CellDetails') ?>
				</div>
			</td>
		</tr>
		<tr>
			<td class="layout">
				<div>
					<?php echo $this->getFileContents('page_admin_badge_CurrentCell') ?>
				</div>
			</td>
		</tr>
	</table>		
</div>