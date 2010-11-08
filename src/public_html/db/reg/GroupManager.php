<?php

class db_reg_GroupManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'RegistrationGroup';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_reg_GroupManager();
		}
		
		return self::$instance;
	}
	
	public function createGroup() {
		$sql = '
			INSERT INTO
				RegistrationGroup()
			VALUES()
		';

		$this->execute($sql, array(), 'Create registration group.');
		
		return $this->lastInsertId();
	}
}

?>