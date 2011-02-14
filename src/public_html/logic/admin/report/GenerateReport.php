<?php

class logic_admin_report_GenerateReport extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($reportId) {
		$report = $this->strictFindById(db_ReportManager::getInstance(), $reportId);
		
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
			
		// special reports will build on the basic report results computed above.
		if($report['isPaymentsToDate'] === 'T') {
			$newRows = array();
			
			$info['headings'] = array_merge(array('Reg Group ID', 'Registration ID'), $info['headings']);
			$info['headings'] = array_merge($info['headings'], array(
				'Payment Date', 'Amount', 'Payment Method', 'Payment Info', 'Transaction ID' 
			));
			
			$emptyPayment = array('', '', '', '', '');
			$emptyRegistration = array_fill(0, count($info['headings'])-count($emptyPayment), '');
		
			$regGroups = db_reg_GroupManager::getInstance()->findByEventId($report['eventId']);
			foreach($regGroups as $regGroup) {
				$payments = $regGroup['payments'];
				$registrations = $regGroup['registrations'];
				$num = max(count($payments), count($registrations));
				
				for($i=0; $i<$num; ++$i) {
					// registration and user fields.
					$augmentedRow = array($regGroup['id']);
					if(empty($registrations[$i])) {
						$augmentedRow = array_merge($augmentedRow, $emptyRegistration);
					}
					else {
						$regId = $registrations[$i]['id'];
						$augmentedRow[] = $regId;
						
						foreach($info['rows'] as $index => $row) {
							if($row['registrationId'] == $regId) {
								$augmentedRow = array_merge($augmentedRow, $info['rows'][$index]['data']);
								break;
							}
						}
					}	
					// payment fields.
					if(empty($payments[$i])) {
						$augmentedRow = array_merge($augmentedRow, $emptyPayment);
					}
					else {
						$augmentedRow[] = $payments[$i]['transactionDate'];
						$augmentedRow[] = $payments[$i]['amount'];
											
						switch($payments[$i]['paymentTypeId']) {
							case model_PaymentType::$CHECK:
								$paymentInfo = $payments[$i]['checkNumber'];
								$method = 'Check';
								break;
							case model_PaymentType::$PO:
								$paymentInfo = $payments[$i]['purchaseOrderNumber'];
								$method = 'PO';
								break;
							case model_PaymentType::$AUTHORIZE_NET:
								$paymentInfo = 'Ending '.$payments[$i]['cardSuffix'];
								$method = $payments[$i]['cardType'];
								break;
						}
						
						$augmentedRow[] = $method;
						$augmentedRow[] = $paymentInfo;
						$augmentedRow[] = $payments[$i]['transactionId'];
					}
					
					// add in other required stuff so our interface is consistant.
					$newRows[] = array(
						'regGroupId' => $regGroup['id'],
						'registrationId' => isset($regId)? $regId : null,
						'data' => $augmentedRow
					);
				}
			}
			
			// overwrite with augmented data.
			$info['rows'] = $newRows;
		}

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