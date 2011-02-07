<?php

class action_admin_emailTemplate_EditEmailTemplate extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_emailTemplate_EditEmailTemplate();
		$this->converter = new viewConverter_admin_emailTemplate_EditEmailTemplate();
	}
	
	public function view() {
		$id = RequestUtil::getValue('id', 0);
		
		$template = $this->logic->view($id);
		
		return $this->converter->getView(array(
			'emailTemplate' => $template
		));
	}
	
	public function saveEmailTemplate() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$template = RequestUtil::getParameters(array(
			'id',
			'enabled',
			'contactFieldId',
			'fromAddress',
			'bcc',
			'subject',
			'header',
			'footer'
		));
		
		$regTypeIds = RequestUtil::getValueAsArray('regTypeIds', array(-1));
		
		$this->logic->saveEmailTemplate($template, $regTypeIds);
		
		return new fragment_Success();
	}
	
	public function sendTestEmail() {
		
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