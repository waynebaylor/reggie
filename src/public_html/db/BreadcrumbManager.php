<?php

class db_BreadcrumbManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	protected function getTableName() {
		return '';
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_BreadcrumbManager();
		}
		
		return self::$instance;
	}
	
	public function findSectionCrumbs($id) {
		$sql = '
			SELECT
				Event.id as eventId,
				Event.code as eventCode,
				Page.id as pageId,
				Section.id as sectionId
			FROM
				Event
			INNER JOIN
				Page
			ON
				Event.id = Page.eventId
			INNER JOIN
				Section
			ON
				Page.id = Section.pageId
			WHERE
				Section.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find section breadcrumbs.');
	}
	
	public function findRegTypeCrumbs($id) {
		$sql = '
			SELECT
				Event.id as eventId,
				Event.code as eventCode,
				Page.id as pageId,
				Section.id as sectionId,
				RegType.id as regTypeId
			FROM
				Event
			INNER JOIN
				Page
			ON
				Event.id = Page.eventId
			INNER JOIN
				Section
			ON
				Page.id = Section.pageId
			INNER JOIN
				RegType
			ON
				Section.id = RegType.sectionId
			WHERE
				RegType.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find reg type breadcrumbs.');
	}
	
	public function findContactFieldCrumbs($id) {
		$sql = '
			SELECT
				Event.id as eventId,
				Event.code as eventCode,
				Page.id as pageId,
				Section.id as sectionId,
				ContactField.id as contactFieldId
			FROM
				Event
			INNER JOIN
				Page
			ON
				Event.id = Page.eventId
			INNER JOIN
				Section
			ON
				Page.id = Section.pageId
			INNER JOIN
				ContactField
			ON
				Section.id = ContactField.sectionId
			WHERE
				ContactField.id = :id
		';
		
		$params = array(
			'id' => $id
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find contact field breadcrumbs.');
	}
}

?>