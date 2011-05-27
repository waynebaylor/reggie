<?php

class logic_admin_staticPage_EditPage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$page = db_StaticPageManager::getInstance()->find($params['id']);
		$eventInfo = db_EventManager::getInstance()->findInfoById($page['eventId']);
		
		return array(
			'page' => $page,
			'eventCode' => $eventInfo['code']
		);
	}
	
	public function savePage($params) {
		$fixedContent = $this->purifyHtml($params['content']);
		
		db_StaticPageManager::getInstance()->save(array(
			'id' => $params['id'],
			'name' => $params['name'],
			'title' => $params['title'],
			'content' => $fixedContent
		));
		
		return $params;
	}
}

?>