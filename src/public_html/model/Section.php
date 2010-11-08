<?php

class model_Section
{
	public static function containsRegTypes($section) {
		return self::containsContentType($section, model_ContentType::$REG_TYPE);
	}
	
	public static function containsContactFields($section) {
		return self::containsContentType($section, model_ContentType::$CONTACT_FIELD);
	}
	
	public static function containsRegOptions($section) {
		return self::containsContentType($section, model_ContentType::$REG_OPTION);
	}
	
	public static function containsVariableQuantityOptions($section) {
		return self::containsContentType($section, model_ContentType::$VAR_QUANTITY_OPTION);
	}
	
	/**
	 * whether or not the given section's content is of the given
	 * type.
	 * @param $section
	 * @param model_ContentType $type
	 */
	private static function containsContentType($section, $type) {
		return intval($section['contentType']['id'], 10) === $type;
	}
}

?>