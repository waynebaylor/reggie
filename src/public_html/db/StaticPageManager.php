<?php

class db_StaticPageManager extends db_Manager 
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_StaticPageManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		
	}
	
	public function findByEventId($eventId) {
		
	}
}