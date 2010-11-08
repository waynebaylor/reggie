<?php

class model_Validation
{
	public static $REQUIRED = 1;
	public static $MIN_LENGTH = 2;
	public static $MAX_LENGTH = 3;
	
	public static function values() {
		$manager = db_ValidationManager::getInstance();
		return $manager->findAll();
	}
	
	public static function valueOf($id) {
		$m = db_ValidationManager::getInstance();
		$rule = $m->find($id);
		
		if(empty($rule)) {
			return NULL;
		}
		else {
			return $rule;
		}
	}
}

?>