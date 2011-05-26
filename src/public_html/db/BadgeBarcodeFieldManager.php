<?php

class db_BadgeBarcodeFieldManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_BadgeBarcodeFieldManager();
		}
		
		return self::$instance;
	}
	
	public function findByBadgeCellId($id) {
		return $this->select(
			'BadgeBarcodeField', 
			array(
				'id',
				'badgeCellId',
				'contactFieldId'
			), 
			array(
				'badgeCellId' => $id
			)
		);
	}
	
	public function deleteBadgeBarcodeField($id) {
		$this->del('BadgeBarcodeField', array('id' => $id));
	}
	
	public function deleteByBadgeCellId($id) {
		$this->del('BadgeBarcodeField', array('badgeCellId' => $id));
	}
}

?>