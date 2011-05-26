<?php

class page_admin_badge_Helper
{
	public static function getRegTypes($t) {
		if($t['appliesToAll'] === true) {
			return 'All';
		}
		else {
			$names = array();
			foreach($t['appliesTo'] as $regType) {
				$names[] = "({$regType['code']}) {$regType['description']}";
			}
			
			return implode('<br>', $names);
		}
	}
}