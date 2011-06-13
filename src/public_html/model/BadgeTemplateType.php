<?php

class model_BadgeTemplateType
{
	private static $types =  array(
		array(
			'name' => '3 x 8 Single',
			'code' => 'ThreeByFourDouble',
			'className' => 'badgeTemplateType_ThreeByFourDouble'
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
	
	public static function newTemplate($code) {
		$t = self::valueOf($code);
		return new $t['className'];
	}
}

?>