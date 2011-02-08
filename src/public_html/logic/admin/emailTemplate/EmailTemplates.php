<?php

class logic_admin_emailTemplate_EmailTemplates extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($eventId) {
		return db_EmailTemplateManager::getInstance()->findByEventId($eventId);
	}
	
	public function addEmailTemplate($template, $regTypeIds) {
		db_EmailTemplateManager::getInstance()->createEmailTemplate($template, $regTypeIds);

		return db_EmailTemplateManager::getInstance()->findByEventId($template['eventId']);
	}
	
	public function removeEmailTemplate($id) {
		$template = $this->strictFindById(db_EmailTemplateManager::getInstance(), $id);
		db_EmailTemplateManager::getInstance()->delete($id);
		
		return db_EmailTemplateManager::getInstance()->findByEventId($template['eventId']);
	}
}

?>