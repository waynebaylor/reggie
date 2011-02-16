<?php

class logic_admin_report_PaymentsToDateHelper
{
	public static function addSpecialInfo($report, $info) {
		// special reports will build on the basic report results computed above.
		$newRows = array();
		
		$info['headings'] = array_merge(array('Reg Group ID', 'Registration ID'), $info['headings']);
		$info['headings'] = array_merge($info['headings'], array(
			'Payment Date', 'Amount', 'Payment Method', 'Payment Info', 'Transaction ID' 
		));
		
		$emptyPayment = array('', '', '', '', '');
		$emptyRegistration = array_fill(0, count($info['headings'])-count($emptyPayment)-1, ''); // -1 because we always have RegGroupID value.
	
		$regGroups = db_reg_GroupManager::getInstance()->findByEventId($report['eventId']);
		foreach($regGroups as $regGroup) {
			$payments = $regGroup['payments'];
			$registrations = $regGroup['registrations'];
			$num = max(count($payments), count($registrations));
			
			for($i=0; $i<$num; ++$i) {
				unset($regId); // reset so it's not inadvertantly used below.
				
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
					$augmentedRow[] = '$'.number_format($payments[$i]['amount'], 2);
										
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
		
		return $info;
	}
}

?>