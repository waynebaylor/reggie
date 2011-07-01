<?php

class db_BadgeCellManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public function getTableName() {
		// this manager manages badge cells and cell content. only
		// the cell content is orderable, so we can use the cell
		// content table name here.
		return 'BadgeCell_TextContent';	
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_BadgeCellManager();
		}
		
		return self::$instance;
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['content'] = $this->findBadgeCellContentByCellId($obj['id']);
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
		$this->del('BadgeBarcodeField', array('badgeCellId' => $id));
		$this->del('BadgeCell', array('id' => $id));
	}
	
	public function findBadgeCellContentByCellId($id) {
		$sql = '
			SELECT
				BadgeCell_TextContent.id,
				BadgeCell_TextContent.badgeCellId,
				BadgeCell_TextContent.displayOrder,
				BadgeCell_TextContent.text,
				BadgeCell_TextContent.contactFieldId,
				ContactField.displayName as contactFieldName
			FROM
				BadgeCell_TextContent
			LEFT JOIN
				ContactField
			ON
				BadgeCell_TextContent.contactFieldId = ContactField.id
			WHERE
				BadgeCell_TextContent.badgeCellId = :badgeCellId
			ORDER BY
				BadgeCell_TextContent.displayOrder
		';
		
		$params = array('badgeCellId' => $id);
		
		return $this->rawQuery($sql, $params, 'Find badge cell content.');
	}
	
	public function findBadgeCellContentById($id) {
		$sql = '
			SELECT
				BadgeCell_TextContent.id,
				BadgeCell_TextContent.badgeCellId,
				BadgeCell_TextContent.displayOrder,
				BadgeCell_TextContent.text,
				BadgeCell_TextContent.contactFieldId,
				ContactField.displayName as contactFieldName
			FROM
				BadgeCell_TextContent
			LEFT JOIN
				ContactField
			ON
				BadgeCell_TextContent.contactFieldId = ContactField.id
			WHERE
				BadgeCell_TextContent.id = :id
		';
		
		$params = array('id' => $id);
		
		return $this->rawQueryUnique($sql, $params, 'Find badge cell content by id.');
	}
	
	public function deleteByTemplateId($templateId) {
		$cells = $this->findByBadgeTemplateId($templateId);
		
		foreach($cells as $cell) {
			$this->deleteBadgeCell($cell['id']);
		}
	}
	
	public function moveCellContentUp($cellContent) {
		$this->moveUp($cellContent, 'badgeCellId', $cellContent['badgeCellId']);
	}
	
	public function moveCellContentDown($cellContent) {
		$this->moveDown($cellContent, 'badgeCellId', $cellContent['badgeCellId']);
	}
}

?>