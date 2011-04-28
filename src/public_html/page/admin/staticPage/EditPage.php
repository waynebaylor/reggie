
<script type="text/javascript">
	dojo.require("hhreg.xhrEditForm");
	dojo.require("dijit.InlineEditBox");
	dojo.require("dijit.form.Textarea");
	
	dojo.addOnLoad(function() {
		var input = dojo.byId("static-content-input");
		var value = dojo.byId("static-content-value");
		
		var e = new dijit.InlineEditBox({
			editor: "dijit.form.Textarea",
			value: value.innerHTML,
			buttonSave: "Preview",
			autoSave: false,
			renderAsHtml: true,
			noValueIndicator: '<span style="color:#666;"><b>Click to enter content.</b> Any HTML you type will be rendered when you click &quot;Preview&quot;.</span>',
			onChange: function(text) {
				input.value = text;
			}
		}, dojo.byId("static-page-content"));	
		e.startup();

		value.parentNode.removeChild(value);
	});
</script>

<style type="text/css">
#static-content-editor {
	padding: 5px;
	border: 1px solid #ddf;
	border-radius: 10px;
	background-color: #efe;
}
</style>

<div id="content">
	<div class="fragment-edit">
		<h3>
			<?php echo $this->title ?>
		</h3>
		
		<?php echo $this->xhrTableForm(
			'/admin/staticPage/EditPage',
			'savePage',
			$this->getFileContents('page_admin_staticPage_Edit')		
		) ?>
	</div>
</div>
