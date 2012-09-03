<?php

class db_FeedbackManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_FeedbackManager();
		}
		
		return self::$instance;
	}
	
	public function save($params) {
		$sql = '
			INSERT INTO 
				Feedback (
					feedback,
					type,
					status
				)
			VALUES (
				:feedback,
				:type,
				:status
			)
		';
		
		$params = array(
			'feedback' => $params['feedback'],
			'type' => 'COMMENT',
			'status' => 'UNRESOLVED'
		);
		
		$this->execute($sql, $params, 'Save feedback.');
	}
}

?>