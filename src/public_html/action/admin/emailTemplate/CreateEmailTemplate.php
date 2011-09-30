<?php

class action_admin_emailTemplate_CreateEmailTemplate extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_CreateEmailTemplate();
		$this->converter = new viewConverter_admin_emailTemplate_CreateEmailTemplate();
	}
	
	public function view() {
		
	}
	
public function addEmailTemplate() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$template = RequestUtil::getParameters(array(
			'eventId',
			'enabled',
			'contactFieldId',
			'fromAddress',
			'bcc',
			'subject',
			'header',
			'footer'
		));
		
		$regTypeIds = RequestUtil::getValueAsArray('regTypeIds', array(-1));
		
		$updatedTemplates = $this->logic->addEmailTemplate($template, $regTypeIds);
		
		return $this->converter->getAddEmailTemplate(array(
			'emailTemplates' => page_admin_emailTemplate_Helper::convert($updatedTemplates)
		));
	}
	
public function addEmailTemplate($template, $regTypeIds) {
		db_EmailTemplateManager::getInstance()->createEmailTemplate($template, $regTypeIds);

		return db_EmailTemplateManager::getInstance()->findByEventId($template['eventId']);
	}

public function getAddEmailTemplate($properties) {
		$this->setProperties($properties);
		
		$list = $this->getFileContents('page_admin_emailTemplate_List');
		
		return new template_TemplateWrapper($list);
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);

		// check if there is overlap between templates.
		$regTypeIds = RequestUtil::getValueAsArray('regTypeIds', array());
		$existingTemplates = db_EmailTemplateManager::getInstance()->findByEventId(RequestUtil::getValue('eventId', 0));
		foreach($existingTemplates as $template) {
			if(model_EmailTemplate::hasOverlap($template, $regTypeIds)) {
				$errors['regTypeIds[]'] = 'Registration Types conflict with existing template.'; 
			}
		}	

		return $errors;
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'enabled',
				'value' => RequestUtil::getValue('enabled', false),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Status is required.'
					)
				)
			),
			array(
				'name' => 'contactFieldId',
				'value' => RequestUtil::getValue('contactFieldId', null),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Contact Field is required.'
					)
				)
			),
			array(
				'name' => 'fromAddress',
				'value' => RequestUtil::getValue('fromAddress', null),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'From Address is required.'
					)
				)
			),
			array(
				'name' => 'regTypeIds[]',
				'value' => RequestUtil::getValueAsArray('regTypeIds', array()),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Registration Types is required.'
					)
				)
			)
		);
	}
}

?>