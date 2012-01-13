<?php


class validation_admin_Payment
{
	public static function getConfig() {
		return array(
			validation_Validator::required('checkNumber', 'Check Number is required.'),
			validation_Validator::required('purchaseOrderNumber', 'Purchase Order Number is required.'),
			validation_Validator::required('amount', 'Amount is required.'),
			validation_Validator::required('cardNumber', 'Card Number is required.'),
			validation_Validator::required('firstName', 'First Name is required.'),
			validation_Validator::required('lastName', 'Last Name is required.')
		);
	}
	
	public static function validate($values) {
		// validate based on payment type.
		if($values['paymentType'] == model_PaymentType::$CHECK) {
			$errors = validation_Validator::validate(self::getConfig(), ArrayUtil::keyIntersect($values, array(
				'amount', 
				'checkNumber'
			)));
		}
		else if($values['paymentType'] == model_PaymentType::$PO) {
			$errors = validation_Validator::validate(self::getConfig(), ArrayUtil::keyIntersect($values, array(
				'amount', 
				'purchaseOrderNumber'
			)));
		}
		else if($values['paymentType'] == model_PaymentType::$AUTHORIZE_NET) {
			$errors = validation_Validator::validate(self::getConfig(), ArrayUtil::keyIntersect($values, array(
				'amount', 
				'cardNumber',
				'firstName',
				'lastName'
			)));
		}
		
		return $errors;
	}
}

?>