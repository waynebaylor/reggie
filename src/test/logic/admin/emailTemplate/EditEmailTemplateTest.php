<?php

class logic_admin_emailTemplate_EditEmailTemplateTest extends PHPUnit_Framework_TestCase
{
	protected function setUp() {
		$this->event = db_EventManager::getInstance()->find(1);
		$this->logic = new logic_admin_emailTemplate_EditEmailTemplate();
	}
	public function testView() {
		$this->logic->view(1);
	}
	
	public function testSaveEmailTemplate() {
		$emailTemplate = current($this->event['emailTemplates']);
		$emailField = null;
		
		$fields = model_Event::getInformationFields($this->event);
		foreach($fields as $field) {
			if($field['formInput']['id'] == model_FormInput::$TEXT) {
				$emailField = $field;		
			}
		}
		
		$template = array(
			'id' => $emailTemplate['id'],
			'enabled' => 'T',
			'contactFieldId' => $emailField['id'],
			'fromAddress' => 'unit@test.ccc',
			'bcc' => 'unit@test.ccc',
			'subject' => 'the subject',
			'header' => 'header',
			'footer' => 'footer'
		);
		
		$regTypeIds = array(-1);
		
		$this->logic->saveEmailTemplate($template, $regTypeIds);
	}
	
	public function testSendTestEmail() {
		$emailTemplate = current($this->event['emailTemplates']);
		
		$this->logic->sendTestEmail($emailTemplate['id'], 'unit@test.ccc');
	}
}

?>