<?php

class db_BadgeCellManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_BadgeCellManager();
		}
		
		return self::$instance;
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['content'] = $this->findBadgeCellContentById($obj['id']);
		$obj['barcodeFields'] = db_BadgeBarcodeFieldManager::getInstance()->findByBadgeCellId($obj['id']);
		
		return $obj;
	}
	
	public function find($id) {
		return $this->selectUnique(
			'BadgeCell', 
			array(
				'id',
				'badgeTemplateId',
				'xCoord',
				'yCoord',
				'width',
				'font',
				'fontSize',
				'horizontalAlign',
				'hasBarcode'
			), 
			array(
				'id' => $id
			)
		);
	}
	
	public function findByBadgeTemplateId($id) {
		return $this->select(
			'BadgeCell', 
			array(
				'id',
				'badgeTemplateId',
				'xCoord',
				'yCoord',
				'width',
				'font',
				'fontSize',
				'horizontalAlign',
				'hasBarcode'
			), 
			array(
				'badgeTemplateId' => $id
			)
		);
	}
	
	public function createBadgeCell($data) {
		$this->insert(
			'BadgeCell', 
			ArrayUtil::keyIntersect($data, array(
				'badgeTemplateId',
				'xCoord',
				'yCoord',
				'width',
				'font',
				'fontSize',
				'horizontalAlign',
				'hasBarcode'
			))
		);
		
		return $this->lastInsertId();
	}
	
	public function saveBadgeCell($data) {
		$id = $data['id'];
		unset($data['id']);
		
		$this->update('BadgeCell', $data, array('id' => $id));
	}
	
	public function addText($data) {
		$data['displayOrder'] = $this->getNextOrder();

		$this->insert(
			'BadgeCell_TextContent',
			ArrayUtil::keyIntersect($data, array(
				'badgeCellId',
				'displayOrder',
				'text'
			))		
		);
	}
	
	public function addInformationField($data) {
		$data['displayOrder'] = $this->getNextOrder();

		$this->insert(
			'BadgeCell_TextContent',
			ArrayUtil::keyIntersect($data, array(
				'badgeCellId',
				'displayOrder',
				'contactFieldId'
			))		
		);
	}
	
	public function deleteBadgeCellContent($id) {
		$this->del('BadgeCell_TextContent', array('id' => $id));
	}
	
	public function deleteBadgeCell($id) {
		$this->del('BadgeCell_TextContent', array('badgeCellId' => $id));
		$this->del('BadgeCell', array('id' => $id));
	}
	
	private function getNextOrder() {
		$n = $this->rawSelect(
			'BadgeCell_TextContent', 
			array(
				'MAX(displayOrder) as maxOrder'
			), 
			array()
		);
		
		return max(1, $n[0]['maxOrder']+1);
	}
	
	public function findBadgeCellContentById($id) {
		return $this->rawSelect(
			'BadgeCell_TextContent', 
			array(
				'id',
				'badgeCellId',
				'displayOrder',
				'text',
				'contactFieldId'	
			), 
			array(
				'badgeCellId' => $id
			)
		);
	}
}

?>