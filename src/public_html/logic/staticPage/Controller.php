<?php

class logic_staticPage_Controller extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$page = db_StaticPageManager::getInstance()->findByEventCodeAndName($params['eventCode'], $params['name']);
		
		return array(
			'title' => $page['title'],
			'content' => $page['content']
		);
	}
}

?>