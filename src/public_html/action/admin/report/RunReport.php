<?php

class action_admin_report_RunReport extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		if($id === 'payments_to_date') {
			$eventId = RequestUtil::getValue('eventId', 0);
			$regGroups = db_reg_GroupManager::getInstance()->findByEventId($eventId);
			
			$csv = array('Group Id, Registration Id, Date Cancelled, Last Name, First Name, Amount, Payment Date, Payment Info, Transaction Id, Payment Method');
			
			$emptyPayment = ',,,,';
			$emptyRegistration = ',,,';
			
			foreach($regGroups as $regGroup) {
				$payments = $regGroup['payments'];
				$registrations = $regGroup['registrations'];
				$num = max(count($payments), count($registrations));
				
				for($i = 0; $i<$num; ++$i) {
					$line = $regGroup['id'].',';
					
					if(empty($registrations[$i])) {
						$line .= $emptyRegistration;
					}
					else {
						$dateCancelled = $registrations[$i]['dateCancelled'];
						$lastName = model_Registrant::getInformationValue($registrations[$i], array('id'=>'193'));
						$firstName = model_Registrant::getInformationValue($registrations[$i], array('id'=>'191'));
						$line .= "{$registrations[$i]['id']},{$dateCancelled},{$lastName},{$firstName}";
					}
					
					$line .= ',';
					
					if(empty($payments[$i])) {
						$line .= $emptyPayment;
					}
					else {
						$amount = $payments[$i]['amount'];
						$date = $payments[$i]['transactionDate'];
						$transactionId = $payments[$i]['transactionId'];
						switch($payments[$i]['paymentTypeId']) {
							case model_PaymentType::$CHECK:
								$info = $payments[$i]['checkNumber'];
								$method = 'Check';
								break;
							case model_PaymentType::$PO:
								$info = $payments[$i]['purchaseOrderNumber'];
								$method = 'PO';
								break;
							case model_PaymentType::$AUTHORIZE_NET:
								$info = 'Ending '.$payments[$i]['cardSuffix'];
								$method = $payments[$i]['cardType'];
								break;
						}
						
						$line .= "{$amount},{$date},{$info},{$transactionId},{$method}";
					}
					
					$csv[] = $line;
				}
			}
			
			$csv = implode(PHP_EOL, $csv);
			return new template_TemplateWrapper("<pre>{$csv}</pre>");
		}
		else {
			$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('id', 0));
			$event = $this->strictFindById(db_EventManager::getInstance(), $report['eventId']);
			
			$fieldHeadings = db_ReportManager::getInstance()->getReportFieldNames($report);
			
			$results = db_ReportManager::getInstance()->generateReport($report);
			
			return new template_admin_ReportResults($event, $report, $fieldHeadings, $results);
		}
	}
}

?>