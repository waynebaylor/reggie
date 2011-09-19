<?php

class action_admin_fileUpload_FileUpload extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_fileUpload_FileUpload();
		$this->converter = new viewConverter_admin_fileUpload_FileUpload();
	}

	public function checkRole($user, $eventId, $method='') {
		$hasRole = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));	
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, model_Role::$EVENT_MANAGER, $eventId);
		
		if(!$hasRole) {
			throw new Exception('User does not have required role.');
		}
	}
	
	public function view() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function listFiles() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0)
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->listFiles($params);
		return $this->converter->getListFiles($info);
	}
	
	public function deleteFiles() {
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'fileNames' => RequestUtil::getValueAsArray('fileNames', array())
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->deleteFiles($params);
		return $this->converter->getDeleteFiles($info);
	}
	
	public function saveFile() { 
		$params = array(
			'eventId' => RequestUtil::getValue('eventId', 0),
			'file' => $_FILES['file']
		);
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->saveFile($params);
		return $this->converter->getSaveFile($info);
	}
}

?>