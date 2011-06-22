
<script type="text/javascript">
	dojo.require("hhreg.xhrAddList");
	dojo.require("hhreg.xhrTableForm");

	(function() {
		var removeCellConfirmation = function() {
			// confirmation for remove cell links.
			dojo.query("a.remove-cell-link").connect("onclick", function(event) {
				if(!confirm("Are you sure?")) {
					dojo.stopEvent(event);
				}
			});
		};
		
		dojo.addOnLoad(function() {
			dojo.query(".fragment-edit-general form").forEach(function(item) {
				hhreg.xhrTableForm.bind(item);
			});
			
			dojo.query(".fragment-current-cell form").forEach(function(item) {
				hhreg.xhrTableForm.bind(item, function(response) {
					var div = dojo.create("div", {innerHTML: response});
					dojo.addClass(div, "hide");
					dojo.body().appendChild(div);
	
					var preview = dojo.byId("save-cell-preview");
					preview = dojo.place(preview, dojo.byId("badge-preview"), "only");
					dojo.attr(preview, "id", "");
					dojo.removeClass(preview, "hide");
	
					div.parentNode.removeChild(div);
				});
			});
			
			dojo.query(".fragment-cells").forEach(function(item) {
				hhreg.xhrAddList.bind(item, function() {
					removeCellConfirmation();
				});
			});
			
			dojo.query(".fragment-cell-details").forEach(function(item) {
				hhreg.xhrAddList.bind(item, function() { 
					var cellsDiv = dojo.query("#badge-cells .fragment-list")[0].parentNode;
	
					// replace the template cells list with the new list included in the response. also need to 
					// remove the id attribute so we won't interfere with subsequent user actions.
					dojo.query("#add-content-template-cells").place(cellsDiv, "replace").removeClass("hide").attr("id", "");
	
					removeCellConfirmation();
				});
			});
	
			// add template cell form.
			dojo.query("#badge-cells .fragment-add input[name=contentType]").forEach(function(item) {
				dojo.connect(dojo.byId("contentType_field"), "onclick", function() {
					dojo.query("#badge-cells .fragment-add .content-specifics").addClass("hide");
					dojo.removeClass(dojo.byId("content-field"), "hide");
				});
	
				dojo.connect(dojo.byId("contentType_text"), "onclick", function() {
					dojo.query("#badge-cells .fragment-add .content-specifics").addClass("hide");
					dojo.removeClass(dojo.byId("content-text"), "hide");
				});
	
				dojo.connect(dojo.byId("contentType_barcode"), "onclick", function() {
					dojo.query("#badge-cells .fragment-add .content-specifics").addClass("hide");
					dojo.removeClass(dojo.byId("content-barcode"), "hide");
				});
			});
	
			// add cell content form.
			dojo.query("#cell-content .fragment-add input[name=contentType]").forEach(function(item) {
				dojo.connect(dojo.byId("contentType_field_cell_content"), "onclick", function() {
					dojo.query("#cell-content .fragment-add .content-specifics").addClass("hide");
					dojo.removeClass(dojo.byId("content-field-cell-content"), "hide");
				});
	
				dojo.connect(dojo.byId("contentType_text_cell_content"), "onclick", function() {
					dojo.query("#cell-content .fragment-add .content-specifics").addClass("hide");
					dojo.removeClass(dojo.byId("content-text-cell-content"), "hide");
				});
			});
	
			removeCellConfirmation();
		});
	})();
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
			<td id="badge-cells" class="layout" rowspan="2">
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
				<div>
					<h3>Preview</h3>
					<?php echo $this->templateType->getHtml($this->template, $this->selectedCell['id']) ?>
				</div>
			</td>
			<td id="cell-content" class="layout" rowspan="2">
				<div class="fragment-cell-details">
					<div>
						<?php echo $this->getFileContents('page_admin_badge_CellDetails') ?>
					</div>
					
					<div class="sub-divider"></div>
					
					<?php if(!empty($this->selectedCell)): ?>
					<div class="fragment-add">
						<?php echo $this->xhrAddForm(
							'Add Content',
							'/admin/badge/EditBadgeTemplate',
							'addCellContent',
							$this->getFileContents('page_admin_badge_AddCellContent')
						) ?>
					</div>
					<?php endif; ?>
				</div>
			</td>
		</tr>
		<tr>
			<td id="current-cell" class="layout">
				<h3>Cell Position/Alignment</h3>
				
				<div class="fragment-current-cell">
					<?php if(!empty($this->selectedCell)): ?>
					<?php echo $this->xhrTableForm(
						'/admin/badge/EditBadgeTemplate',
						'saveCellDetails',
						$this->getFileContents('page_admin_badge_CurrentCell')
					) ?>				
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</table>		
</div>