<?php

require_once 'db/AttributeManager.php';

class model_Attribute
{
	public static $SIZE = 1;
	public static $COLS = 2;
	public static $ROWS = 3;
	
	public static function values() {
		$m = db_AttributeManager::getInstance();
		return $m->findAll();
	}
	
	public static function valueOf($id) {
		$m = db_AttributeManager::getInstance();
		$attr = $m->find($id);
		
		if(empty($attr)) {
			return NULL;
		}
		else {
			return $attr;	
		}
	}
}

?>