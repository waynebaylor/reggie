<?php

class action_admin_fileUpload_FileUpload extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_fileUpload_FileUpload();
		$this->converter = new viewConverter_admin_fileUpload_FileUpload();
	}

	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);
		$info = $this->logic->view(array('eventId' => $eventId));
		return $this->converter->getView($info);
	}
	
	public function listFiles() {
		$eventId = RequestUtil::getValue('eventId', 0);
		$info = $this->logic->listFiles(array('eventId' => $eventId));
		return $this->converter->getListFiles($info);
	}
	
	public function deleteFiles() {
		$eventId = RequestUtil::getValue('eventId', 0);
		$fileNames = RequestUtil::getValueAsArray('fileNames', array());
		$info = $this->logic->deleteFiles(array(
			'eventId' => $eventId,
			'fileNames' => $fileNames
		));
		return $this->converter->getDeleteFiles($info);
	}
	
	public function saveFile() { 
		$info = $this->logic->saveFile(array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'file' => $_FILES['file']
		));
		
		return $this->converter->getSaveFile($info);
	}
}

?>