<?php

class action_admin_registration_Payment extends action_ValidatorAction
{
	public function view() {
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), RequestUtil::getValue('id', 0));
		$report = $this->strictFindById(db_ReportManager::getInstance(), RequestUtil::getValue('reportId', 0));
		$event = $this->strictFindById(db_EventManager::getInstance(), $report['eventId']);
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), RequestUtil::getValue('groupId', 0));
		
		return new template_admin_EditPayment($event, $report, $group, $payment);
	}
	
	public function savePayment() {
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), RequestUtil::getValue('id', 0));
		
		$paymentTypeId = $payment['paymentTypeId'];
		if(!in_array($paymentTypeId, array(model_PaymentType::$CHECK, model_PaymentType::$PO))) {
			throw new Exception('Error editing payment. Only checks and purchse orders may be edited.');
		}
		
		$errors = $this->validate($this->getPaymentFieldNames($paymentTypeId));
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$p = RequestUtil::getParameters(array('id', 'checkNumber', 'purchaseOrderNumber'));
		$p['paymentReceived'] = RequestUtil::getValue('paymentReceived', 'false');
		$p['paymentTypeId'] = $payment['paymentTypeId'];
		db_reg_PaymentManager::getInstance()->save($p);
		
		return new fragment_Success();
	}
	
	public function addPayment() { 
		$errors = $this->validate($this->getPaymentFieldNames(RequestUtil::getValue('paymentType', 0)));
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), RequestUtil::getValue('regGroupId', 0));

		$r = reset($group['registrations']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $r['eventId']);
		
		$payment = RequestUtil::getParameters(array(
			'paymentType',
			'amount',
			'checkNumber',
			'purchaseOrderNumber',
			'cardNumber',
			'month', 
			'year',
			'firstName',
			'lastName',
			'address',
			'city',
			'state',
			'zip',
			'country'
		));
			
		if($payment['paymentType'] == model_PaymentType::$AUTHORIZE_NET) {
			$authorizeNet = new payment_AuthorizeNET($event, $payment, $payment['amount']);
			$result = $authorizeNet->makePayment();
			
			$result['paymentType'] = model_PaymentType::$AUTHORIZE_NET;
			$result['name'] = $payment['firstName'].' '.$payment['lastName'];
			$result = array_merge(
				$result, 
				ArrayUtil::keyIntersect($payment, array('address', 'city', 'state', 'zip', 'country', 'amount'))
			);
			
			$payment = $result;
		}
				
		db_reg_PaymentManager::getInstance()->createPayment($group['id'], $payment);
		
		return new fragment_editRegistrations_payment_List($event, $group);
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'checkNumber',
				'value' => RequestUtil::getValue('checkNumber', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Check Number is required.'
					)
				)
			),
			array(
				'name' => 'purchaseOrderNumber',
				'value' => RequestUtil::getValue('purchaseOrderNumber', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Purchase Order Number is required.'
					)
				)
			),
			array(
				'name' => 'amount',
				'value' => RequestUtil::getValue('amount', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Amount is required.'
					)
				)
			),
			array(
				'name' => 'cardNumber',
				'value' => RequestUtil::getValue('cardNumber', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Card Number is required.'
					)
				)
			),
			array(
				'name' => 'firstName',
				'value' => RequestUtil::getValue('firstName', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'First Name is required.'
					)
				)
			),
			array(
				'name' => 'lastName',
				'value' => RequestUtil::getValue('lastName', ''),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Last Name is required.'
					)
				)
			)
		);
	}
	
	private function getPaymentFieldNames($paymentTypeId) {
		$fields = array('amount');
		
		if($paymentTypeId == model_PaymentType::$CHECK) {
			$fields[] = 'checkNumber';
		}
		else if($paymentTypeId == model_PaymentType::$PO) {
			$fields[] = 'purchaseOrderNumber';
		}
		else if($paymentTypeId == model_PaymentType::$AUTHORIZE_NET) {
			$fields = array_merge($fields, array(
				'cardNumber',
				'firstName',
				'lastName'
			));
		}
		
		return $fields;
	}
}

?>