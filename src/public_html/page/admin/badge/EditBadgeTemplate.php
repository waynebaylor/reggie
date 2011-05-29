
<style type="text/css">
#template-layout h3 {
	margin-top: 10px;
}

#template-layout {
	width: 100%;
	height: 100%;
	border-collapse: collapse;
}

#template-layout td {
	border: 1px solid #999;
	padding-left: 5px;
	vertical-align: top;
}

#badge-canvas {
	height: 216pt;
	width: 288pt;
	border: 1px solid black;
	margin: 0 auto 10px;
	background-color: white;
}

#badge-preview {
	background-color: #eee;
}
</style>

<div id="content">
	<div id="edit-badge-workarea">
		<h3><?php echo $this->title ?></h3>
		
		<table id="template-layout">
			<tr>
				<td id="badge-cells" rowspan="2">
					<h3>Badge Cells</h3>
					<div>
						<?php echo $this->getFileContents('page_admin_badge_TemplateCells') ?>
					</div>
				</td>
				<td id="badge-preview">
					<h3>Preview</h3>
					<div id="badge-canvas"></div>
				</td>
				<td id="cell-details" rowspan="2">
					<h3>Cell Details</h3>
				</td>
			</tr>
			<tr>
				<td id="current-cell">
					<h3>Current Cell</h3>
				</td>
			</tr>
		</table>		
	</div>
</div>