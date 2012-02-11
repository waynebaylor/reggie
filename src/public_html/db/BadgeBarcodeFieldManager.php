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
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId]
	 */
	public function findByBadgeCellId($params) {
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
				BadgeBarcodeField.badgeCellId = :badgeCellId
			AND
				ContactField.eventId = :eventId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId'));
		
		return $this->query($sql, $params, 'Find badge barcode fields.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, id]
	 */
	public function deleteBadgeBarcodeField($params) {
		$sql = '
			DELETE FROM
				BadgeBarcodeField
			WHERE
				BadgeBarcodeField.id = :id
			AND
				BadgeBarcodeField.contactFieldId 
			IN (
				SELECT ContactField.id 
				FROM ContactField
				WHERE ContactField.id = BadgeBarcodeField.contactFieldId
				AND ContactField.eventId = :eventId
			)
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));
		
		$this->execute($sql, $params, 'Delete barcode field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId]
	 */
	public function deleteByBadgeCellId($params) {
		$sql = '
			DELETE FROM
				BadgeBarcodeField
			WHERE
				BadgeBarcodeField.badgeCellId = :badgeCellId
			AND
				BadgeBarcodeField.contactFieldId 
			IN (
				SELECT ContactField.id 
				FROM ContactField
				WHERE ContactField.id = BadgeBarcodeField.contactFieldId
				AND ContactField.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId'));
		
		$this->execute($sql, $params, 'Delete barcode field.');
	}
	
	/**
	 * 
	 * @param array $params [eventId, badgeCellId, contactFieldId]
	 */
	public function addInformationField($params) {
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
				BadgeCell.id = :badgeCellId
			AND
				BadgeTemplate.eventId = :eventId
		';
		
		$results = $this->rawQuery($sql, ArrayUtil::keyIntersect($params, array('eventId', 'badgeCellId')), 'Check badge barcode permission.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to modify BadgeBarcodeField. (event id, badge cell id) -> ({$params['eventId']}, {$params['badgeCellId']}).");	
		}
		
		$this->insert(
			'BadgeBarcodeField',
			ArrayUtil::keyIntersect($params, array(
				'badgeCellId',
				'contactFieldId'
			))
		);
	}
}

?>