<?php

class page_admin_emailTemplate_Helper
{
	public static function convert($emailTemplates) { 
		$templates = array();
		
		foreach($emailTemplates as $t) {
			$template = new stdClass();
			
			$template->id = $t['id'];
			$template->enabled = $t['enabled'] === 'true'? 'Enabled' : 'Disabled';
			
			$field = db_ContactFieldManager::getInstance()->find($t['contactFieldId']);
			$template->fieldName = $field['displayName'];
			
			$template->fromAddress = $t['fromAddress'];
			$template->bcc = $t['bcc'];
			$template->availableTo = self::getAvailableTo($t);
			
			$templates[] = $template;
		}
		
		return $templates;
	}
	
	private static function getAvailableTo($t) { 
		if($t['availableToAll'] === true) {
			return 'All';
		}
		
		$names = array();
		foreach($t['availableTo'] as $regType) {
			$names[] = "({$regType['code']}) {$regType['description']}";
		}
		
		return implode('<br>', $names);
	}
}

?>