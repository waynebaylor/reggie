<?php

class action_admin_fileUpload_FileUpload extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		$eventId = RequestUtil::getValue('id', 0);
		$event = $this->strictFindById(db_EventManager::getInstance(), $eventId);
		
		$html = <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.widget.FileUploadGrid");
				
				new hhreg.admin.widget.FileUploadGrid({
					eventId: {$event['id']}
				}, dojo.place("<div></div>", dojo.byId("upload-grid"), "replace")).startup();
			</script>
			<div id="upload-grid"></div>
_;

		return new template_TemplateWrapper($html);
	}
	
	public function saveFile() {
		$eventId = $_REQUEST['id'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}
		
		$file = $_FILES['file'];
		
		// check the uploaded file extension.
		$fileInfo = pathinfo($file['name']);
		$extension = $fileInfo['extension'];
		if(in_array($extension, $this->getAllowedExtensions())) {
			FileUtil::saveEventFile($event, $file);
		}
		
		return new template_Redirect('/admin/fileUpload/FileUpload?action=view&id='.$eventId);
	}
	
	public function deleteFile() {
		$eventId = $_REQUEST['id'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}
		
		FileUtil::deleteEventFile($event, $_REQUEST['fileName']);
		
		return new template_Redirect('/admin/fileUpload/FileUpload?action=view&id='.$eventId);
	}
	
	private function getAllowedExtensions() {
		return array(
			'pdf', 'png', 'jpg', 'gif', 'jpeg', 'doc', 'txt', 'xls', 'tif'
		); 
	}
}

?>