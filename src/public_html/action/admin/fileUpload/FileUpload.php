<?php

class action_admin_fileUpload_FileUpload extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}

	public function view() {
		$eventId = $_REQUEST['id'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}

		return new template_admin_FileUpload($event);
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
		
		return new template_Redirect('/action/admin/fileUpload/FileUpload?action=view&id='.$eventId);
	}
	
	public function deleteFile() {
		$eventId = $_REQUEST['id'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		if(empty($event)) {
			return new template_ErrorPage();
		}
		
		FileUtil::deleteEventFile($event, $_REQUEST['fileName']);
		
		return new template_Redirect('/action/admin/fileUpload/FileUpload?action=view&id='.$eventId);
	}
	
	private function getAllowedExtensions() {
		return array(
			'pdf', 'jpg', 'gif', 'jpeg', 'doc', 'txt', 'xls'
		); 
	}
}

?>