<?php

class logic_admin_staticPage_PageList extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
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
			'pages' => $urlAdded
		);
	}
	
	public function addPage($params) {
		db_StaticPageManager::getInstance()->createPage(array(
			'eventId' => $params['eventId'],
			'name' => $params['name'],
			'title' => $params['title']
		));
		
		return $this->view(array(
			'eventId' => $params['eventId']
		));
	}
	
	public function removePage($params) {
		$page = db_StaticPageManager::getInstance()->find($params['id']);
		
		db_StaticPageManager::getInstance()->deletePage($params['id']);
		
		return $this->view(array(
			'eventId' => $page['eventId']
		));
	}
}

?>