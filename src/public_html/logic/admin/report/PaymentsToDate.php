<?php

class logic_admin_report_PaymentsToDate extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($eventId) {
		$regGroups = db_reg_GroupManager::getInstance()->findByEventId($eventId);
		
		$paymentData = array();
		
		$paymentData[] = array(
			'Group Id', 
			'Registration Id', 
			'Date Cancelled', 
			'Last Name', 
			'First Name', 
			'Amount', 
			'Payment Date', 
			'Payment Info', 
			'Transaction Id', 
			'Payment Method'
		);
		
		$emptyPayment = array('', '', '', '', '');
		$emptyRegistration = array('', '', '', '');
		
		foreach($regGroups as $regGroup) {
			$payments = $regGroup['payments'];
			$registrations = $regGroup['registrations'];
			$num = max(count($payments), count($registrations));
			
			for($i = 0; $i<$num; ++$i) {
				$line = array();
				
				$line[] = $regGroup['id'];
				
				if(empty($registrations[$i])) {
					$line = array_merge($line, $emptyRegistration);
				}
				else {
					$line[] = $registrations[$i]['id'];
					$line[] = $registrations[$i]['dateCancelled'];
					$line[] = model_Registrant::getInformationValue($registrations[$i], array('id'=>'193'));
					$line[] = model_Registrant::getInformationValue($registrations[$i], array('id'=>'191'));
				}
				
				if(empty($payments[$i])) {
					$line = array_merge($line, $emptyPayment);
				}
				else {
					$line[] = $payments[$i]['amount'];
					$line[] = $payments[$i]['transactionDate'];
					
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
					
					$line[] = $info;
					$line[] = $payments[$i]['transactionId'];
					$line[] = $method;
				}
				
				$paymentData[] = $line;
			}
		}
		
		return $paymentData;
	}
	
	public function csv($eventId) {
		return $this->view($eventId);
	}
}

?>