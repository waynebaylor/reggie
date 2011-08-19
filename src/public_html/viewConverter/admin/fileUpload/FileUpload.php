<?php

class viewConverter_admin_fileUpload_FileUpload extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
	}
	
	public function getView($properties) {
		$this->setProperties($properties);
		
		$html = <<<_
			<div id="upload-grid"></div>
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.FileUploadGrid");
				
				new hhreg.admin.widget.FileUploadGrid({
					eventId: {$this->eventId}
				}, dojo.place("<div></div>", dojo.byId("upload-grid"), "replace")).startup();
			</script>
_;

		return new template_TemplateWrapper($html);
	}
	
	public function getListFiles($properties) {
		$this->setProperties($properties);
		
		$html = $this->getFileContents('page_admin_data_Files');
		return new template_TemplateWrapper($html);
	}
	
	public function getSaveFile($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
	
	public function getDeleteFiles($properties) {
		$this->setProperties($properties);
		
		return new fragment_Success();
	}
}

?>