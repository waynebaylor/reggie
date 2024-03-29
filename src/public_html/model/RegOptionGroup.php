<?php

class model_RegOptionGroup
{
	/**
	 * Option groups directly under a section have the 'sectionId' property;
	 * option groups under options have the 'regOptionId' property. 
	 */
	public static function isSectionGroup($group) {
		return !empty($group['sectionId']);
	}
	
	public static function hasOptionsVisible($group, $regTypeId) {
		foreach($group['options'] as $option) {
			$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
			if(!empty($price)) {
				return true;
			}
		}
		
		return false;
	}
}

?>