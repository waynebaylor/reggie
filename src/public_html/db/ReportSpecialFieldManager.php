<?php

class db_ReportSpecialFieldManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ReportSpecialFieldManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * @param array $params [eventId, reportId]
	 */
	public function findByReport($params) {
		$sql = '
			SELECT
				RSF.id,
				RSF.reportId,
				RSF.name,
				RSF.displayName
			FROM
				Report_SpecialField RSF
			INNER JOIN
				Report R
			ON
				RSF.reportId = R.id
			WHERE
				R.eventId = :eventId
			AND
				RSF.reportId = :reportId
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'reportId'
		));
		
		return $this->query($sql, $params, 'Find special fields by report.');
	}
	
	/**
	 * @param array $params [eventId, reportId, name]
	 */
	public function createField($params) {
		$this->checkReportPermission($params);
		
		$sql = '
			INSERT INTO
				Report_SpecialField (
					reportId,
					name,
					displayName
				)
			VALUES (
				:reportId,
				:name,
				:displayName
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'reportId',
			'name'
		));
		$params['displayName'] = model_ReportSpecialField::getDisplayName($params['name']);
		
		$this->execute($sql, $params, 'Create report special field.');
		
		return $this->lastInsertId();
	}
	
	/**
	 * @param array $params [eventId, id]
	 */
	public function deleteField($params) {
		$sql = '
			DELETE FROM
				Report_SpecialField
			WHERE
				Report_SpecialField.id = :id
			AND
				Report_SpecialField.reportId 
			IN (
				SELECT Report.id 
				FROM Report 
				WHERE Report.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'id'
		));
		
		$this->execute($sql, $params, 'Delete report special field.');
	}
	
	/**
	 * @param array $params [eventId, reportId, name]
	 */
	public function deleteFieldByName($params) {
		$sql = '
			DELETE FROM
				Report_SpecialField
			WHERE
				Report_SpecialField.name = :name
			AND
				Report_SpecialField.reportId = :reportId
			AND
				Report_SpecialField.reportId 
			IN (
				SELECT Report.id 
				FROM Report 
				WHERE Report.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'reportId',
			'name'
		));
		
		$this->execute($sql, $params, 'Delete special field.');
	}
	
	/**
	 * @param array $params [eventId, reportId]
	 */
	public function deleteByReport($params) {
		$sql = '
			DELETE FROM
				Report_SpecialField 
			WHERE
				Report_SpecialField.reportId = :reportId
			AND
				Report_SpecialField.reportId 
			IN (
				SELECT Report.id 
				FROM Report 
				WHERE Report.eventId = :eventId
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array(
			'eventId',
			'reportId'
		));
		
		$this->execute($sql, $params, 'Delete report special fields.');
	}
	
	private function checkReportPermission($params) {
		$sql = '
			SELECT id 
			FROM Report 
			WHERE eventId = :eventId 
			AND id = :reportId
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'reportId'));
		$results = $this->rawQuery($sql, $params, 'Check report permissions.');
		
		if(count($results) === 0) {
			throw new Exception("Permission denied to Report_SpecialField: (event id, report id) -> ({$params['eventId']}, {$params['reportId']}).");
		}
	}
}

?>