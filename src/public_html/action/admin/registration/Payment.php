<?php

class action_admin_registration_Payment extends action_ValidatorAction
{
	public function view() {
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), RequestUtil::getValue('id', 0));
		
		return new template_admin_EditPayment($payment);
	}
	
	public function savePayment() {
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), RequestUtil::getValue('id', 0));
		
		if($payment['paymentTypeId'] == model_PaymentType::$CHECK) {
			$fields = array('checkNumber');
		}
		else if($payment['paymentTypeId'] == model_PaymentType::$PO) {
			$fields = array('purchaseOrderNumber');
		}
		else {
			throw new Exception('Error editing payment. Only checks and purchse orders may be edited.');
		}
		
		$errors = $this->validate($fields);
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
			)
		);
	}
}

?>