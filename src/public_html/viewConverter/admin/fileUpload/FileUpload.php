<?php

class viewConverter_admin_fileUpload_FileUpload extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Event Files';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.FileUploadGrid");
				
				dojo.addOnLoad(function() {
					new hhreg.admin.widget.FileUploadGrid({
						eventId: {$this->eventId}
					}, dojo.place("<div></div>", dojo.byId("upload-grid"), "replace")).startup();
				});
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					
					<div id="upload-grid"></div>
				</div>
			</div>
_;

		return $body;
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