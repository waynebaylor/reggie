<?php

class model_Category
{
	public static function values() {
		$m = db_CategoryManager::getInstance();
		return $m->findAll();
	}
	
	public static function valueOf($id) {
		$categories = model_Category::values();
		foreach($categories as $category) {
			if(intval($category['id'], 10) === intval($id, 10)) {
				return $category;
			}
		}
		
		return NULL;
	}
	
	/**
	 * The category code used in URLs.
	 * @param $category
	 */
	public static function code($category) {
		return strtolower(substr($category['displayName'], 0, 2));
	}
}

?>