<?php

class logic_admin_report_GenerateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($reportId, $searchTerm = '', $searchFieldId = 0) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		
		$info = $this->getBaseInfo($report, $searchTerm, $searchFieldId);
		$info['event'] = db_EventManager::getInstance()->find($info['eventId']);
		
		if($report['isPaymentsToDate'] === 'T') {
			$info = logic_admin_report_PaymentsToDateHelper::addSpecialInfo($report, $info);
			$info['showSearchLink'] = false;
		}
		else if($report['isAllRegToDate'] === 'T') { 
			$info = logic_admin_report_AllRegToDateHelper::addSpecialInfo($report, $info);
			$info['showCreateRegLink'] = false;
		}
		else if($report['isOptionCount'] === 'T') {
			$info = logic_admin_report_OptionCountHelper::addSpecialInfo($report, $info);
			$info['showCreateRegLink'] = false;
			$info['showSearchLink']	= false;
		}
		else if($report['isRegTypeBreakdown'] === 'T') {
			$info = logic_admin_report_RegTypeBreakdownHelper::addSpecialInfo($report, $info);
			$info['showCreateRegLink'] = false;
			$info['showSearchLink'] = false;
		}
		
		return $info;
	}
	
	public function getBaseInfo($report, $searchTerm, $searchFieldId) {
		$fieldHeadings = db_ReportManager::getInstance()->findReportFieldHeadings($report['id']);
		
		// null regIds indicates that we should return all values.
		if(empty($searchTerm) || empty($searchFieldId)) {
			$regIds = null;
		}
		else {
			$regIds = db_ReportManager::getInstance()->findRegIdsMatchingSearch($report['eventId'], $searchTerm, $searchFieldId);
		}
		
		$fieldValues = db_ReportManager::getInstance()->findReportFieldValues($report['id']); 
		$registrationValues = db_ReportManager::getInstance()->findReportRegistrationValues($report['id']); 
		$paymentValues = db_ReportManager::getInstance()->findReportPaymentValues($report['id']); 
		
		$headings = $this->getHeadings($report, $fieldHeadings); 
		$values = $this->getValues(array(
			'report' => $report,
			'registrationIds' => $regIds,
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
			'rows' => $values,
			'registrationIds' => $regIds
		);
			
		return $info;
	}
	
	public function csv($reportId) {
		return $this->view($reportId);
	}
	
	public function search($params) {
		return $this->view($params['reportId'], $params['term'], $params['contactFieldId']);
	}
	
	private function getValues($params) { 
		$values = array();
		$processedGroupIds = array();
		
		foreach($params['registrationValues'] as $reg) {
			// if we're doing a search, then skip this reg if they're not in the search results.
			if(isset($params['registrationIds']) && !in_array($reg['registrationId'], $params['registrationIds'])) {
				continue;
			}
			
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
}

?>