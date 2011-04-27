<?php

class logic_admin_staticPage_PageList extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		$pages = db_StaticPageManager::getInstance()->findByEventId($params['eventId']);
		
		$urlAdded = array();
		foreach($pages as $p) {
			$protocol = 'http://';
			$url = $this->contextUrl('/pages/'.$this->event['code'].'/'.$p['name']);
			$link = $protocol.$_SERVER['SERVER_NAME'].$url;
			
			$p['url'] = $list;
			$urlAdded[] = $p;
		}
		
		return array(
			'eventId' => $params['eventId'],
			'pages' => $pages
		);
	}
	
	public function eventPages() {
		
	}
	
	public function create() {
		
	}
	
	public function save() {
		
	}
	
	public function remove() {
		
	}
}

?>