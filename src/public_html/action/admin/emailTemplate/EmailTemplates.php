<?php

class action_admin_emailTemplate_EmailTemplates extends action_ValidatorAction
{
	private $logic;
	
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_EmailTemplates();
		$this->converter = new viewConverter_admin_emailTemplate_EmailTemplates();
	}
	
	public function view() {
		$eventId = RequestUtil::getValue('eventId', 0);
		
		$emailTemplates = $this->logic->view($eventId);
		
		return $this->converter->getView(array(
			'eventId' => $eventId,
			'emailTemplates' => page_admin_emailTemplate_Helper::convert($emailTemplates)
		));
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
			'bcc'
		));
		
		$regTypeIds = RequestUtil::getValueAsArray('regTypeIds', array(-1));
		
		$updatedTemplates = $this->logic->addEmailTemplate($template, $regTypeIds);
		
		return $this->converter->getAddEmailTemplate(array(
			'emailTemplates' => page_admin_emailTemplate_Helper::convert($updatedTemplates)
		));
	}
	
	public function removeEmailTemplate() {
		$emailTemplateId = RequestUtil::getValue('id', 0);
		
		$updatedTemplates = $this->logic->removeEmailTemplate($emailTemplateId);
		
		return $this->converter->getAddEmailTemplate(array(
			'emailTemplates' => page_admin_emailTemplate_Helper::convert($updatedTemplates)
		));
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
				'name' => 'regTypeIds',
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