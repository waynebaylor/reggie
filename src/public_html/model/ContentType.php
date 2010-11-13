<?php

// enum-like class for Content Types. 
class model_ContentType
{
	// content type constants taken from ContentType 
	// database table.
	public static $REG_TYPE = 1;
	public static $CONTACT_FIELD = 2;
	public static $REG_OPTION = 3;
	public static $TEXT = 4;
	public static $VAR_QUANTITY_OPTION = 5;
	
	// returns all the content type values.
	public static function values() {
		$m = db_ContentTypeManager::getInstance();
		return $m->findAll();
	}
	
	// returns the content type with the given id, or 
	// NULL if none exists.
	public static function valueOf($id) {
		foreach(self::values() as $type) {
			if(intval($type['id'], 10) === intval($id, 10)) {
				return $type;
			}
		}
		
		return NULL;
	}
}

?>