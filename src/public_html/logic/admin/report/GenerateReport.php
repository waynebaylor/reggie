<?php

class logic_admin_report_GenerateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($reportId) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		
		$info = $this->getBaseInfo($report);
		
		if($report['isPaymentsToDate'] === 'T') {
			$info = logic_admin_report_PaymentsToDateHelper::addSpecialInfo($report, $info);
		}
		else if($report['isAllRegToDate'] === 'T') { 
			$info = logic_admin_report_AllRegToDateHelper::addSpecialInfo($report, $info);
		}
		else if($report['isOptionCount'] === 'T') {
			$info = logic_admin_report_OptionCountHelper::addSpecialInfo($report, $info);
			$info['showCreateRegLink'] = false;
		}
		else if($report['isRegTypeBreakdown'] === 'T') {
			$info = logic_admin_report_RegTypeBreakdownHelper::addSpecialInfo($report, $info);
			$info['showCreateRegLink'] = false;
		}
		
		return $info;
	}
	
	public function getBaseInfo($report) {
		$fieldHeadings = db_ReportManager::getInstance()->findReportFieldHeadings($report['id']);
		$fieldValues = db_ReportManager::getInstance()->findReportFieldValues($report['id']); 
		$registrationValues = db_ReportManager::getInstance()->findReportRegistrationValues($report['id']); 
		$paymentValues = db_ReportManager::getInstance()->findReportPaymentValues($report['id']); 
		
		$headings = $this->getHeadings($report, $fieldHeadings); 
		$values = $this->getValues($report, $registrationValues, $fieldHeadings, $fieldValues, $paymentValues);
		
		$info = array(
			'eventId' => $report['eventId'],
			'reportId' => $report['id'],
			'reportName' => $report['name'],
			'headings' => $headings,
			'rows' => $values
		);
			
		return $info;
	}
	
	public function csv($reportId) {
		return $this->view($reportId);
	}
	
	private function getValues($report, $registrationValues, $fieldHeadings, $fieldValues, $paymentValues) { 
		$values = array();
		$processedGroupIds = array();
		
		foreach($registrationValues as $reg) {
			$value = array();
			// registration values.
			if($report['showDateRegistered'] === 'T') {
				$value[] = $reg['dateRegistered'];
			}
			if($report['showDateCancelled'] === 'T') {
				$value[] = $reg['dateCancelled'];
			}
			if($report['showCategory'] === 'T') {
				$value[] = $reg['categoryName'];
			}
			if($report['showRegType'] === 'T') {
				$value[] = $reg['regTypeName'];
			}
			// user selected field values.
			// field headings have the order.
			foreach($fieldHeadings as $fieldHeading) { 
				$fieldId = $fieldHeading['id']; 
				$fieldValue = ArrayUtil::getValue($fieldValues[$reg['registrationId']], $fieldId, ''); // value may be an array.
				$value[] = is_array($fieldValue)? implode(', ', $fieldValue) : $fieldValue; // if value is array convert to comma list.
			}
			// payment values (only show once per registration group.
			$hideValue = in_array($reg['groupId'], $processedGroupIds);
			if($report['showTotalCost'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($paymentValues[$reg['groupId']]['cost'], 2);
			}
			if($report['showTotalPaid'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($paymentValues[$reg['groupId']]['paid'], 2);
			}
			if($report['showRemainingBalance'] === 'T') {
				$value[] = $hideValue? '' : '$'.number_format($paymentValues[$reg['groupId']]['balance'], 2);
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