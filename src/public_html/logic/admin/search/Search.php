<?php

class logic_admin_search_Search extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventInfo = db_EventManager::getInstance()->findInfoById($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $eventInfo['code'],
			'eventId' => $params['eventId'],
			'searchTerm' => $params['searchTerm']
		);
	}
	
	public function listResults($params) {
		// since the query uses LIKE '<term>%', if term is empty then our query
		// will return everything. we don't allow empty searches, so don't execute 
		// the search.
		if(empty($params['searchTerm'])) {
			$results = array();
		}
		else {
			$results = db_reg_InformationManager::getInstance()->searchInformationValues($params);
		}
		
		return array(
			'eventId' => $params['eventId'],
			'searchTerm' => $params['searchTerm'],
			'results' => $results
		);	
	}
}

?>