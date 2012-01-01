<?php

class db_BreadcrumbManager extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
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
	
	public function findEmailTemplatesCrumbs($eventId) {
		$sql = '
			SELECT
				code
			FROM 
				Event
			WHERE
				id = :id
		';
		
		$params = array(
			'id' => $eventId
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find email templates breadcrumbs.');
	}
	
	public function findEditEmailTemplateCrumbs($emailTemplateId) {
		$sql = '
			SELECT
				EmailTemplate.eventId,
				Event.code
			FROM
				EmailTemplate
			INNER JOIN
				Event
			ON
				Event.id = EmailTemplate.eventId
			WHERE
				EmailTemplate.id = :emailTemplateId
		';
		
		$params = array(
			'emailTemplateId' => $emailTemplateId
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find edit email template breadcrumbs.');
	}
	
	public function findGenerateReportCrumbs($reportId) {
		$sql = '
			SELECT
				Event.code
			FROM
				Event
			INNER JOIN
				Report
			ON
				Event.id = Report.eventId
			WHERE
				Report.id = :reportId
		';
		
		$params = array(
			'reportId' => $reportId
		);
		
		return $this->rawQueryUnique($sql, $params, 'Find generate report breadcrumbs.');
	}
	
	/**
	 * returns an array of reg group and reg option ids. ids are ordered by
	 * placement in the hierarchy ending with the given reg option id and 
	 * working backward up the hierarchy from there.
	 * @param number $regOptionId the reg option to start with
	 */
	public function getGroupsAndOpts($regOptionId) {
		$ids = array($regOptionId);
		
		$option = db_RegOptionManager::getInstance()->find($regOptionId);
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		$ids[] = $group['id'];
		
		if(!model_RegOptionGroup::isSectionGroup($group)) {
			$tmp = $this->getGroupsAndOpts($group['regOptionId']);
			$tmp = array_reverse($tmp);
			$ids = array_merge($ids, $tmp);
		}
		
		return array_reverse($ids);
	}
}

?>