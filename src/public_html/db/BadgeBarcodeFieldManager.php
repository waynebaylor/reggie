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
		$sql = '
			SELECT 
				BadgeBarcodeField.id,
				BadgeBarcodeField.badgeCellId,
				BadgeBarcodeField.contactFieldId,
				ContactField.displayName as contactFieldName
			FROM
				BadgeBarcodeField
			INNER JOIN
				ContactField
			ON
				BadgeBarcodeField.contactFieldId = ContactField.id
			WHERE
				badgeCellId = :badgeCellId
		';
		
		$params = array(
			'badgeCellId' => $id
		);
		
		return $this->query($sql, $params, 'Find badge barcode fields.');
	}
	
	public function deleteBadgeBarcodeField($id) {
		$this->del('BadgeBarcodeField', array('id' => $id));
	}
	
	public function deleteByBadgeCellId($id) {
		$this->del('BadgeBarcodeField', array('badgeCellId' => $id));
	}
	
	public function addInformationField($data) {
		$this->insert(
			'BadgeBarcodeField',
			ArrayUtil::keyIntersect($data, array(
				'badgeCellId',
				'contactFieldId'
			))
		);
	}
}

?>