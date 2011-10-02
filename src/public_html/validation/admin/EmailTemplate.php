<?php

class validation_admin_EmailTemplate
{
	public static function getConfig() {
		return array(
			validation_Validator::required('enabled', 'Status is required.'),
			validation_Validator::required('contactFieldId', 'Contact Field is required.'),
			validation_Validator::required('fromAddress', 'From Address is required.'),
			validation_Validator::required('regTypeIds', 'Registration Types is required.'),
		);
	}
	
	public static function validate($values) {
		$errors = validation_Validator::validate(self::getConfig(), $values);

		// check if there is overlap between templates.
		$existingTemplates = db_EmailTemplateManager::getInstance()->findByEventId($values['eventId']);
		foreach($existingTemplates as $template) {
			$differentTemplate = $template['id'] != $values['id'];
			$regTypesOverlap = model_EmailTemplate::hasOverlap($template, $values['regTypeIds']);
			
			if($differentTemplate && $regTypesOverlap) {
				$errors['regTypeIds[]'] = 'Registration Types conflict with existing template.'; 
			}
		}	

		return $errors;
	}
}
?>