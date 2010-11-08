<?php

class model_PaymentType
{
	public static $CHECK = 1;
	public static $PO = 2;
	public static $AUTHORIZE_NET = 3;
	
	public static function values() {
		return db_payment_PaymentTypeManager::getInstance()->findAll();
	}
	
	public static function valueOf($id) {
		foreach(self::values() as $type) {
			if(intval($id, 10) === intval($type['id'], 10)) {
				return $type;
			}
		}
		
		return NULL;
	}
}

?>