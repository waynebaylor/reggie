<?php

class logic_admin_staticPage_EditPage extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$page = db_StaticPageManager::getInstance()->find($params['id']);
		
		return array(
			'page' => $page
		);
	}
	
	public function savePage($params) {
		$tidy = new tidy();
		
		db_StaticPageManager::getInstance()->save(array(
			'id' => $params['id'],
			'name' => $params['name'],
			'title' => $params['title'],
			'content' => $params['content']
		));
		
		return $params;
	}
}

?>