<?php

class logic_admin_Feedback extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return $params;
	}
	
	public function submitFeedback($params) {
		db_FeedbackManager::getInstance()->save($params);
		
		return $params;
	}
}

?>