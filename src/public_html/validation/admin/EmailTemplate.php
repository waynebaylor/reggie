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

		// check if there is overlap between enabled templates.
		$existingTemplates = db_EmailTemplateManager::getInstance()->findByEventId($values);
		foreach($existingTemplates as $template) {
			$bothTemplatesEnabled = ($values['enabled'] === 'T') && ($template['enabled'] === 'T');
			$differentTemplate = $template['id'] != $values['id'];
			$regTypesOverlap = model_EmailTemplate::hasOverlap($template, $values['regTypeIds']);
			
			if($bothTemplatesEnabled && $differentTemplate && $regTypesOverlap) {
				$errors['regTypeIds[]'] = 'Registration Types conflict with existing template.'; 
			}
		}	

		return $errors;
	}
}
?>