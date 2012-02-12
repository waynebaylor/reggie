<?php

class logic_admin_staticPage_EditPage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) { 
		$page = db_StaticPageManager::getInstance()->findByIdAndEvent($params);
		
		$eventInfo = db_EventManager::getInstance()->findInfoById($page['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $page['eventId'],
			'page' => $page
		);
	}
	
	public function savePage($params) {
		$purifiedContent = $this->purifyHtml($params['content']);
		
		db_StaticPageManager::getInstance()->save(array(
			'eventId' => $params['eventId'],
			'id' => $params['id'],
			'name' => $params['name'],
			'title' => $params['title'],
			'content' => $purifiedContent
		));
		
		return $params;
	}
}

?>