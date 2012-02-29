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

		$obj['content'] = $this->findBadgeCellContentByCellId(array(
			'eventId' => $obj['eventId'],
			'badgeCellId' => $obj['id']
		));
		
		$obj['barcodeFields'] = db_BadgeBarcodeFieldManager::getInstance()->findByBadgeCellId(array(
			'eventId' => $obj['eventId'],
			'badgeCellId' => $obj['id']
		));
		
		return $obj;
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		$sql = '
			SELECT
				BadgeCell.id,
				BadgeCell.badgeTemplateId,
				BadgeCell.xCoord,
				BadgeCell.yCoord,
				BadgeCell.width,
				BadgeCell.font,
				BadgeCell.fontSize,
				BadgeCell.horizontalAlign,
				BadgeCell.hasBarcode,
				BadgeTemplate.eventId
			FROM
				BadgeCell
			INNER JOIN
				BadgeTemplate
			ON
				BadgeCell.badgeTemplateId = BadgeTemplate.id
			WHERE
				BadgeCell.id = :id
			AND
				BadgeTemplate.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		return $this->queryUnique($sql, $params, 'Find badge cell.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId]
	 */
	public function findByBadgeTemplateId($params) { 
		$sql = '
			SELECT
				BadgeCell.id,
				BadgeCell.badgeTemplateId,
				BadgeCell.xCoord,
				BadgeCell.yCoord,
				BadgeCell.width,
				BadgeCell.font,
				BadgeCell.fontSize,
				BadgeCell.horizontalAlign,
				BadgeCell.hasBarcode,
				BadgeTemplate.eventId
			FROM
				BadgeCell
			INNER JOIN
				BadgeTemplate
			ON
				BadgeCell.badgeTemplateId = BadgeTemplate.id
			WHERE
				BadgeCell.badgeTemplateId = :badgeTemplateId
			AND
				BadgeTemplate.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeTemplateId'));
		
		return $this->query($sql, $params, 'Find badge cell.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId, xCoord, 
	 * 						 yCoord, width, font, fontSize, 
	 * 						 horizontalAlign, hasBarcode]
	 */
	public function createBadgeCell($params) { 
		// check if they have access to the badge template
		// before adding the cell.
		$results = $this->rawSelect(
			'BadgeTemplate', 
			array(
				'id', 
				'eventId'
			), 
			array(
				'id' => $params['badgeTemplateId'],
				'eventId' => $params['eventId']
			)
		);
		
		if(count($results) > 0) {
			$this->insert(
				'BadgeCell', 
				ArrayUtil::keyIntersect($params, array(
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
		}
		
		return $this->lastInsertId();
	}
	
	/**
	 * 
	 * @param array $params [eventId, id, xCoord, yCoord, width, font, fontSize,
	 * 						 horizontalAlign, hasBarcode]
	 */
	public function saveBadgeCell($params) { 
		$sql = '
			UPDATE
				BadgeCell
			SET
				xCoord = :xCoord,
				yCoord = :yCoord,
				width = :width,
				font = :font,
				fontSize = :fontSize,
				horizontalAlign = :horizontalAlign
			WHERE
				id = :id
			AND
				badgeTemplateId 
			IN (
				SELECT BadgeTemplate.id
				FROM BadgeTemplate
				WHERE BadgeTemplate.id = badgeTemplateId
				AND BadgeTemplate.eventId = :eventId
					
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id',
			'xCoord',
			'yCoord',
			'width',
			'font',
			'fontSize',
			'horizontalAlign'
		));

		$this->execute($sql, $params, 'Save badge cell.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId]
	 */
	private function checkBadgeCellTextContentPermission($params) {
		$sql = '
			SELECT 
				BadgeTemplate.id,
				BadgeTemplate.eventId
			FROM
				BadgeTemplate
			INNER JOIN
				BadgeCell
			ON
				BadgeTemplate.id = BadgeCell.badgeTemplateId
			INNER JOIN
				BadgeCell_TextContent
			ON
				BadgeCell.id = BadgeCell_TextContent.badgeCellId
			WHERE
				BadgeTemplate.eventId = :eventId
			AND
				BadgeCell.id = :badgeCellId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId'));
		
		$results = $this->rawQuery($sql, $params, 'Badge cell text permission check.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to modify BadgeCell_TextContent: (event id, badge cell id) -> ({$params['eventId']}, {$params['badgeCellId']}).");
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId]
	 */
	private function checkBadgeCellPermission($params) {
		$sql = '
			SELECT 
				BadgeTemplate.id,
				BadgeTemplate.eventId
			FROM
				BadgeTemplate
			INNER JOIN
				BadgeCell
			ON
				BadgeTemplate.id = BadgeCell.badgeTemplateId
			WHERE
				BadgeTemplate.eventId = :eventId
			AND
				BadgeCell.id = :badgeCellId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId'));
		
		$results = $this->rawQuery($sql, $params, 'Badge cell  permission check.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to badge cell: (event id, badge cell id) -> ({$params['eventId']}, {$params['badgeCellId']}).");
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId, text]
	 */
	public function addText($params) { 
		$this->checkBadgeCellPermission($params);
		
		$params['displayOrder'] = $this->getNextOrder();
	
		$this->insert(
			'BadgeCell_TextContent',
			ArrayUtil::keyIntersect($params, array(
				'badgeCellId',
				'displayOrder',
				'text'
			))		
		);
	}
	
	/**
	 * 
	 * @param array $data [eventId, badgeCellId, ...]
	 */
	public function addInformationField($data) {
		$this->checkBadgeCellPermission($data);
		
		$data['displayOrder'] = $this->getNextOrder();

		$data['showRegType'] = ($data['templateField'] === 'registration_type')? 'T' : 'F';
		$data['showLeadNumber'] = ($data['templateField'] === 'lead_number')? 'T' : 'F';
		
		if($data['templateField'] === 'registration_type') {
			$this->insert(
				'BadgeCell_TextContent',
				ArrayUtil::keyIntersect($data, array(
					'badgeCellId',
					'displayOrder',
					'showRegType'
				))
			);
		}
		else if($data['templateField'] === 'lead_number') {
			$this->insert(
				'BadgeCell_TextContent',
				ArrayUtil::keyIntersect($data, array(
					'badgeCellId',
					'displayOrder',
					'showLeadNumber'
				))
			);
		}
		else {
			$data['contactFieldId'] = $data['templateField'];
			
			$this->insert(
				'BadgeCell_TextContent',
				ArrayUtil::keyIntersect($data, array(
					'badgeCellId',
					'displayOrder',
					'contactFieldId'
				))		
			);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteBadgeCellContent($params) {
		$sql = '
			DELETE FROM
				BadgeCell_TextContent
			WHERE
				BadgeCell_TextContent.id = :id
			AND
				BadgeCell_TextContent.badgeCellId 
			IN (
				SELECT BadgeCell.id
				FROM BadgeCell
				INNER JOIN BadgeTemplate
				ON BadgeCell.badgeTemplateId = BadgeTemplate.id
				WHERE BadgeCell.id = BadgeCell_TextContent.badgeCellId
				AND BadgeTemplate.eventId = :eventId
			)
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete badge cell content.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteBadgeCell($params) {
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		// delete badge cell content.
		$sql = '
			DELETE FROM
				BadgeCell_TextContent
			WHERE
				BadgeCell_TextContent.badgeCellId = :id
			AND
				BadgeCell_TextContent.badgeCellId 
			IN (
				SELECT BadgeCell.id
				FROM BadgeCell
				INNER JOIN BadgeTemplate
				ON BadgeCell.badgeTemplateId = BadgeTemplate.id
				WHERE BadgeCell.id = BadgeCell_TextContent.badgeCellId
				AND BadgeTemplate.eventId = :eventId
			)
		';

		$this->execute($sql, $params, 'Delete badge cells.');
		
		// delete badge cell barcode.
		$sql = '
			DELETE FROM
				BadgeBarcodeField
			WHERE
				BadgeBarcodeField.badgeCellId = :id
			AND
				BadgeBarcodeField.badgeCellId 
			IN (
				SELECT BadgeCell.id
				FROM BadgeCell
				INNER JOIN BadgeTemplate
				ON BadgeCell.badgeTemplateId = BadgeTemplate.id
				WHERE BadgeCell.id = BadgeBarcodeField.badgeCellId
				AND BadgeTemplate.eventId = :eventId
			)
		';

		$this->execute($sql, $params, 'Delete badge cell barcode.');
		
		// delete badge cell.
		$sql = '
			DELETE FROM
				BadgeCell
			WHERE
				BadgeCell.id = :id
			AND
				BadgeCell.badgeTemplateId 
			IN (
				SELECT BadgeTemplate.id 
				FROM BadgeTemplate
				WHERE BadgeTemplate.id = BadgeCell.badgeTemplateId
				AND BadgeTemplate.eventId = :eventId
			)
		';
		
		$this->execute($sql, $params, 'Delete badge cell.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId]
	 */
	public function findBadgeCellContentByCellId($params) {
		$this->checkBadgeCellPermission($params);
		
		$sql = '
			SELECT
				BadgeCell_TextContent.id,
				BadgeCell_TextContent.badgeCellId,
				BadgeCell_TextContent.displayOrder,
				BadgeCell_TextContent.showRegType,
				BadgeCell_TextContent.showLeadNumber,
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
		
		$params = ArrayUtil::keyIntersect($params, array('badgeCellId')); 
		
		return $this->rawQuery($sql, $params, 'Find badge cell content.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function findBadgeCellContentById($params) {
		$sql = '
			SELECT
				BadgeCell_TextContent.id,
				BadgeCell_TextContent.badgeCellId,
				BadgeCell_TextContent.displayOrder,
				BadgeCell_TextContent.showRegType,
				BadgeCell_TextContent.showLeadNumber,
				BadgeCell_TextContent.text,
				BadgeCell_TextContent.contactFieldId,
				ContactField.displayName as contactFieldName
			FROM
				BadgeCell_TextContent
			LEFT JOIN
				ContactField
			ON
				BadgeCell_TextContent.contactFieldId = ContactField.id
			INNER JOIN
				BadgeCell
			ON
				BadgeCell_TextContent.badgeCellId = BadgeCell.id
			INNER JOIN
				BadgeTemplate
			ON
				BadgeCell.badgeTemplateId = BadgeTemplate.id
			WHERE
				BadgeTemplate.eventId = :eventId
			AND
				BadgeCell_TextContent.id = :id
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id'
		));
		
		return $this->rawQueryUnique($sql, $params, 'Find badge cell content by id.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeTemplateId]
	 */
	public function deleteByTemplateId($params) {
		$cells = $this->findByBadgeTemplateId($params['badgeTemplateId']);
		
		foreach($cells as $cell) {
			$p = array(
				'eventId' => $params['eventId'], 
				'id' => $cell['id']
			);
			$this->deleteBadgeCell($p);
		}
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId, id]
	 */
	public function moveCellContentUp($params) {
		$this->checkBadgeCellTextContentPermission(ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId')));
		
		$this->moveUp($params, 'badgeCellId', $params['badgeCellId']);
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId, id]
	 */
	public function moveCellContentDown($params) {
		$this->checkBadgeCellTextContentPermission(ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId')));
		
		$this->moveDown($params, 'badgeCellId', $params['badgeCellId']);
	}
	
	/**
	 * Delete badge cell text content for the given badge templates.
	 * @param array $params [eventId, templateIds] 
	 */
	private function deleteBadgeCellTextContentByTemplate($params) {
		$sql = '
			DELETE FROM
				BadgeCell_TextContent
			WHERE
				BadgeCell_TextContent.badgeCellId IN (
					SELECT
						BadgeCell.id 
					FROM
						BadgeCell
					INNER JOIN
						BadgeTemplate
					ON
						BadgeCell.badgeTemplateId = BadgeTemplate.id
					WHERE
						BadgeTemplate.eventId = :eventId
					AND
						BadgeTemplate.id IN (:[templateIds])
				)
		';
		
		$this->execute($sql, $params, 'Delete badge cell text content.');
	}
	
	/**
	 * Delete badge barcode fields for the given badge templates.
	 * @param array $params [eventId, templateIds] 
	 */
	private function deleteBadgeBarcodeFieldByTemplate($params) {
		$sql = '
			DELETE FROM
				BadgeBarcodeField
			WHERE
				BadgeBarcodeField.badgeCellId IN (
					SELECT
						BadgeCell.id 
					FROM
						BadgeCell
					INNER JOIN
						BadgeTemplate
					ON
						BadgeCell.badgeTemplateId = BadgeTemplate.id
					WHERE
						BadgeTemplate.eventId = :eventId
					AND
						BadgeTemplate.id IN (:[templateIds])
				)
		';
		
		$this->execute($sql, $params, 'Delete badge barcode fields.');
	}

	/**
	 * Delete all the badge cells for the given badge templates.
	 * @param array $params [eventId, templateIds] 
	 */
	public function deleteBadgeCellsByTemplate($params) {
		$this->deleteBadgeCellTextContentByTemplate($params);
		$this->deleteBadgeBarcodeFieldByTemplate($params);
		
		$sql = '
			DELETE FROM
				BadgeCell
			WHERE
				BadgeCell.badgeTemplateId IN (
					SELECT
						BadgeTemplate.id
					FROM
						BadgeTemplate
					WHERE
						BadgeTemplate.eventId = :eventId
					AND
						BadgeTemplate.id IN (:[templateIds])
				)
		';
		
		$this->execute($sql, $params, 'Delete badge template cells.');
	}
}

?>