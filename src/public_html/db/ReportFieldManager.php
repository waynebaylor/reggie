<?php

class db_ReportFieldManager extends db_OrderableManager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return 'Report_ContactField';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ReportFieldManager();
		}
		
		return self::$instance;
	}
	
	public function find($id) {
		$sql = '
			SELECT
				Report_ContactField.id,
				Report_ContactField.reportId,
				Report_ContactField.contactFieldId,
				Report_ContactField.displayOrder,
				ContactField.displayName
			FROM
				Report_ContactField
			INNER JOIN
				ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Report_ContactField.id = :id
			ORDER BY
				Report_ContactField.displayOrder
		';

		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find report field.');
	}
	
	public function findByReport($report) {
		$sql = '
			SELECT
				Report_ContactField.id,
				Report_ContactField.reportId,
				Report_ContactField.contactFieldId,
				Report_ContactField.displayOrder,
				ContactField.displayName
			FROM
				Report_ContactField
			INNER JOIN
				ContactField
			ON
				Report_ContactField.contactFieldId = ContactField.id
			WHERE
				Report_ContactField.reportId = :reportId
			ORDER BY
				Report_ContactField.displayOrder
		';
	
		$params = array(
			'reportId' => $report['id']
		);
		
		return $this->query($sql, $params, 'Find report fields.');
	}
	
	public function createField($field) {
		$sql = '
			INSERT INTO
				Report_ContactField (
					reportId,
					contactFieldId,
					displayOrder
				)
			VALUES (
				:reportId,
				:contactFieldId,
				:displayOrder
			)
		';
		
		$params = array(
			'reportId' => $field['reportId'],
			'contactFieldId' => $field['contactFieldId'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create report field.');
	}
	
	public function deleteField($field) {
		$sql = '
			DELETE FROM
				Report_ContactField
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Delete report field.');
	}
	
	public function moveFieldUp($field) {
		$this->moveUp($field, 'reportId', $field['reportId']);
	}
	
	public function moveFieldDown($field) {
		$this->moveDown($field, 'reportId', $field['reportId']);
	}
}

?>