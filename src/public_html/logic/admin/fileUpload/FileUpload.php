<?php

class logic_admin_fileUpload_FileUpload extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'actionMenuEventLabel' => $eventInfo['code']
		);
	}
	
	public function listFiles($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$files = FileUtil::getEventFiles($eventInfo);
		
		$fileData = array();
		foreach($files as $file) {
			$protocol = in_array(Config::$MODE_SSL, Config::$SETTINGS['MODE'])? 'https://' : 'http://';
			$url = Reggie::contextUrl("/files/{$eventInfo['code']}/{$file}");
			$link = $protocol.$_SERVER['SERVER_NAME'].$url;
			
			$fileData[] = array(
				'name' => $file,
				'link' => $link
			);
		}
		
		return array(
			'eventId' => $eventInfo['id'],
			'fileData' => $fileData
		);
	}
	
	public function saveFile($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$file = $params['file'];
		
		// check the uploaded file extension.
		$fileInfo = pathinfo($file['name']);
		$extension = $fileInfo['extension'];
		
		if(in_array($extension, $this->getAllowedExtensions())) {
			FileUtil::saveEventFile($eventInfo, $file);
		}
		
		return array('eventId' => $eventInfo['id']);
	}
	
	public function deleteFiles($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		foreach($params['fileNames'] as $fileName) {
			FileUtil::deleteEventFile($eventInfo, $fileName);	
		}
		
		return array('eventId' => $eventInfo['id']);		
	}
	
	private function getAllowedExtensions() {
		return array(
			'pdf', 'png', 'jpg', 'gif', 'jpeg', 'doc', 'txt', 'xls', 'tif'
		); 
	}
}

?>