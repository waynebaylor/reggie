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
	 * 
	 * @param array $params [eventId, regOptionGroupId]
	 */
	public function findRegOptionGroupCrumbs($params) {
		$group = db_GroupManager::getInstance()->find($params['regOptionGroupId']);

		$groupsAndOpts = $this->getGroupsAndOpts(array(
			'eventId' => $params['eventId'],
			'regOptionId' => $group['regOptionId']
		));
		
		$groupsAndOpts[] = $group['id'];
		
		// the first id in $groupsAndOpts is the section group.
		$sectionGroup = db_GroupManager::getInstance()->find($groupsAndOpts[0]);
		$bc =$this->findSectionCrumbs($sectionGroup['sectionId']);
			
		return array(
			'eventId' => $bc['eventId'],
			'pageId' => $bc['pageId'],
			'sectionId' => $bc['sectionId'],
			'regGroupsAndOpts' => $groupsAndOpts
		);
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionId]
	 */
	public function findRegOptionCrumbs($params) {
		$groupsAndOpts = $this->getGroupsAndOpts($params);
		
		// the first id in $groupsAndOpts is the section group.
		$group = db_GroupManager::getInstance()->find($groupsAndOpts[0]);
		$bc = db_BreadcrumbManager::getInstance()->findSectionCrumbs($group['sectionId']);
			
		return array(
			'eventId' => $bc['eventId'],
			'pageId' => $bc['pageId'],
			'sectionId' => $bc['sectionId'],
			'regGroupsAndOpts' => $groupsAndOpts
		);
	}
	
	public function findVariableRegOptionCrumbs($variableQuantityId) {
		$variableQuantityOption = db_VariableQuantityOptionManager::getInstance()->find($variableQuantityId);
		$bc = self::findSectionCrumbs($variableQuantityOption['sectionId']);
		
		return array(
			'eventId' => $bc['eventId'],
			'pageId' => $bc['pageId'],
			'sectionId' => $bc['sectionId'],
			'variableQuantityOptionId' => $variableQuantityId
		);
	}
	
	/**
	 * 
	 * @param array $params [eventId, regOptionPriceId]
	 */
	public function findRegOptionPriceCrumbs($params) {
		$price = db_RegOptionPriceManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['regOptionPriceId']
		));
		
		if(db_RegOptionPriceManager::getInstance()->isVariableQuantityPrice($price)) {
			$bc = db_BreadcrumbManager::getInstance()->findVariableRegOptionCrumbs($price['regOptionId']);
		}
		else {
			$bc = db_BreadcrumbManager::getInstance()->findRegOptionCrumbs($price);
		}
		
		$bc['regOptionPriceId'] = $params['regOptionPriceId'];
		
		return $bc;
	}
	
	/**
	 * returns an array of reg group and reg option ids. ids are ordered by
	 * placement in the hierarchy ending with the given reg option id and 
	 * working backward up the hierarchy from there.
	 * 
	 * @param array $params [eventId, regOptionId]
	 */
	private function getGroupsAndOpts($params) {
		$ids = array($params['regOptionId']);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['regOptionId']
		));
		
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		$ids[] = $group['id'];
		
		if(!model_RegOptionGroup::isSectionGroup($group)) {
			$tmp = $this->getGroupsAndOpts(array(
				'eventId' => $params['eventId'],
				'regOptionId' => $group['regOptionId']
			));
			
			$tmp = array_reverse($tmp);
			$ids = array_merge($ids, $tmp);
		}
		
		return array_reverse($ids);
	}
}

?>