<?php

class model_Group
{
	/**
	 * Option groups directly under a section have the 'sectionId' property;
	 * option groups under options have the 'regOptionId' property. 
	 */
	public static function isSectionGroup($group) {
		return !empty($group['sectionId']);
	}
}

?>