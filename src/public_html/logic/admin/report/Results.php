<?php

class logic_admin_report_Results extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$eventId = $params['eventId'];
		$reportId = $params['reportId'];
		$searchTerm = isset($params['term'])? $params['term'] : '';
		$searchFieldId = isset($params['contactFieldId'])? $params['contactFieldId'] : 0;
		
		$report = db_ReportManager::getInstance()->findReport(array('eventId' => $eventId, 'id' => $reportId));
		
		$info = $this->getBaseInfo($report, $searchTerm, $searchFieldId);
		$info['event'] = db_EventManager::getInstance()->find($info['eventId']);
		
		$this->showDetailsLink = $this->canSeeDetailsLink($params['user'], $eventId);
		$this->showSummaryLink = $this->canSeeSummaryLink($params['user'], $eventId);
		
		if($report['isPaymentsToDate'] === 'T') {
			$info = logic_admin_report_PaymentsToDateHelper::addSpecialInfo($report, $info);
		}
		else if($report['isAllRegToDate'] === 'T') { 
			$info = logic_admin_report_AllRegToDateHelper::addSpecialInfo($report, $info);
		}
		else if($report['isOptionCount'] === 'T') {
			$info = logic_admin_report_OptionCountHelper::addSpecialInfo($report, $info);
			$this->showDetailsLink = false;
			$this->showSummaryLink = false;
		}
		else if($report['isRegTypeBreakdown'] === 'T') {
			$info = logic_admin_report_RegTypeBreakdownHelper::addSpecialInfo($report, $info);
			$this->showDetailsLink = false;
			$this->showSummaryLink = false;
		}
		
		return array( 
			'showDetailsLink' => $this->showDetailsLink,
			'showSummaryLink' => $this->showSummaryLink,
			'eventId' => $eventId,
			'reportId' => $reportId,
			'info' => $info
		);
	}
	
	private function getBaseInfo($report, $searchTerm, $searchFieldId) {
		$fieldHeadings = db_ReportManager::getInstance()->findReportFieldHeadings($report['id']);
		
		$fieldValues = db_ReportManager::getInstance()->findReportFieldValues($report['id']); 
		$registrationValues = db_ReportManager::getInstance()->findReportRegistrationValues($report['id']); 
		$paymentValues = db_ReportManager::getInstance()->findReportPaymentValues($report['id']); 
		
		$headings = $this->getHeadings($report, $fieldHeadings); 
		$values = $this->getValues(array(
			'report' => $report,
			'registrationValues' => $registrationValues,
			'fieldHeadings' => $fieldHeadings,
			'fieldValues' => $fieldValues,
			'paymentValues' => $paymentValues
		));
		
		$info = array(
			'eventId' => $report['eventId'],
			'reportId' => $report['id'],
			'reportName' => $report['name'],
			'headings' => $headings,
			'rows' => $values
		);
			
		return $info;
	}
	
	private function getHeadings($report, $fieldHeadings) {
		$headings = array();
		// registration fields.
		if($report['showDateRegistered'] === 'T') {
			$headings[] = 'Date Registered';
		}
		if($report['showDateCancelled'] === 'T') {
			$headings[] = 'Date Cancelled';
		}
		if($report['showCategory'] === 'T') {
			$headings[] = 'Category';
		}
		if($report['showRegType'] === 'T') {
			$headings[] = 'Registration Type';
		}
		// user selected fields.
		foreach($fieldHeadings as $fieldHeading) {
			$headings[] = $fieldHeading['displayName'];
		}
		// payment fields.
		if($report['showTotalCost'] === 'T') {
			$headings[] = 'Total Cost';
		}
		if($report['showTotalPaid'] === 'T') {
			$headings[] = 'Total Paid';
		}
		if($report['showRemainingBalance'] === 'T') {
			$headings[] = 'Remaining Balance';
		}
		
		return $headings;
	}
	
	private function getValues($params) {
		$values = array();
		$processedGroupIds = array();
		
		foreach($params['registrationValues'] as $reg) {
			$value = array();
			// registration values.
			if($params['report']['showDateRegistered'] === 'T') {
				$value[] = $reg['dateRegistered'];
			}
			if($params['report']['showDateCancelled'] === 'T') {
				$value[] = $reg['dateCancelled'];
			}
			if($params['report']['showCategory'] === 'T') {
				$value[] = $reg['categoryName'];
			}
			if($params['report']['showRegType'] === 'T') {
				$value[] = $reg['regTypeName'];
			}
			// user selected field values.
			// field headings have the order.
			foreach($params['fieldHeadings'] as $fieldHeading) { 
				$fieldId = $fieldHeading['id']; 
				$fieldValue = ArrayUtil::getValue($params['fieldValues'][$reg['registrationId']], $fieldId, ''); // value may be an array.
				$value[] = is_array($fieldValue)? implode(', ', $fieldValue) : $fieldValue; // if value is array convert to comma list.
			}
			// payment values (only show once per registration group.
			$hideValue = in_array($reg['groupId'], $processedGroupIds);
			if($params['report']['showTotalCost'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($params['paymentValues'][$reg['groupId']]['cost'], 2);
			}
			if($params['report']['showTotalPaid'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($params['paymentValues'][$reg['groupId']]['paid'], 2);
			}
			if($params['report']['showRemainingBalance'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($params['paymentValues'][$reg['groupId']]['balance'], 2);
			}
			
			if(!$hideValue) {
				$processedGroupIds[] = $reg['groupId'];
			}
		
			$values[] = array(
				'regGroupId' => $reg['groupId'],
				'registrationId' => $reg['registrationId'],
				'data' => $value
			);
		}
		
		return $values;
	}
	
	private function canSeeDetailsLink($user, $eventId) {
		$hasRole =  model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN, 
			model_Role::$EVENT_ADMIN
		));
		
		$hasRole = $hasRole || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER, 
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		return $hasRole;
	}
	
	private function canSeeSummaryLink($user, $eventId) {
		return $this->canSeeDetailsLink($user, $eventId) || model_Role::userHasRoleForEvent($user, model_Role::$VIEW_EVENT, $eventId);
	}
}

?>