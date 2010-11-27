<?php

class db_GroupRegistrationManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'GroupRegistration';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_GroupRegistrationManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		
	}
	
	public function findByEvent($event) {
		
	}
	
	public function createGroupRegistration($eventId) {
		
	}
	
	public function save($groupReg) {
		
	}
}

?>