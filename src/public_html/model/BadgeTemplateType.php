<?php

class model_BadgeTemplateType
{
	private static $types =  array(
		array(
			'name' => '3 x 8 Single',
			'code' => 'ThreeByFourDouble',
			'className' => 'badgeTemplateType_ThreeByFourDouble'
		),
		array(
			'name' => '3 x 4',
			'code' => 'ThreeByFour',
			'className' => 'badgeTemplateType_ThreeByFour'
		)
	);
		
	public static function values() {
		return self::$types;
	}
	
	public static function valueOf($code) {
		foreach(self::$types as $type) {
			if($type['code'] === $code) {
				return $type;
			}
		}
		
		return null;
	}
	
	/**
	 * Return an instance of a badge template type based on the code given. The badge template
	 * type objects are used for badge printing.
	 *
	 * @param string $code the badge template code
	 */
	public static function newTemplate($code) {
		$t = self::valueOf($code);
		return new $t['className'];
	}
}

?>