<?php

class logic_admin_staticPage_PageList extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array('eventId' => $params['eventId']);
	}
	
	public function listPages($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$pages = db_StaticPageManager::getInstance()->findByEventId($params['eventId']);
		
		$urlAdded = array();
		foreach($pages as $p) {
			$protocol = 'http://';
			
			$p['href'] = '/pages/'.$p['eventCode'].'/'.$p['name'];
			
			$url = Reggie::contextUrl($p['href']);
			$link = $protocol.$_SERVER['SERVER_NAME'].$url;
			
			$p['url'] = $link;
			$urlAdded[] = $p;
		}
		
		return array(
			'eventId' => $params['eventId'],
			'eventCode' => $eventInfo['code'],
			'pages' => $urlAdded
		);
	}
	
	public function deletePages($params) {
		foreach($params['pageIds'] as $pageId) {
			db_StaticPageManager::getInstance()->deletePage($pageId);
		}

		return array(
			'eventId' => $params['eventId']
		);
	}
}

?>