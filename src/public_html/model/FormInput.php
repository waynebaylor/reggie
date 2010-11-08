<?php

require_once 'db/FormInputManager.php';

class model_FormInput
{
	public static $TEXT = 1;
	public static $TEXTAREA = 2;
	public static $CHECKBOX = 3;
	public static $RADIO = 4;
	public static $SELECT = 5;
	
	public static function values() {
		$m = db_FormInputManager::getInstance();
		return $m->findAll();
	}
	
	public static function valueOf($id) {
		$m = db_FormInputManager::getInstance();
		return $m->find($id);
	}
	
	/**
	 * returns true if the given form input id refers to a 
	 * checkbox, radio button, or select.
	 * @param $formInputId
	 */
	public static function isOptionInput($formInputId) {
		$id = intval($formInputId, 10);
		
		return $id === self::$CHECKBOX || $id === self::$RADIO || $id === self::$SELECT;
	}
}

?>