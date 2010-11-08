<?php

class model_RegType
{
	public static function isVisibleTo($regType, $category) {
		foreach($regType['visibleTo'] as $cat) {
			if(intval($category['id'], 10) === intval($cat['id'], 10)) {
				return true;
			}
		}
		
		return false;
	}
}

?>