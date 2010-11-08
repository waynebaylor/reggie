<?php

class db_ContentTypeManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'ContentType';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ContentTypeManager();
		}
		
		return self::$instance;
	}
	
	public function findAll() {
		$sql = '
			SELECT
				id,
				name
			FROM
				ContentType
		';
		
		return $this->query($sql, array(), 'Find all content types.');
	}
}

?>