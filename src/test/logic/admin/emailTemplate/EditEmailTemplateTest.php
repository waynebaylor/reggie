<?php

class logic_admin_emailTemplate_EditEmailTemplateTest extends logic_admin_emailTemplate_Base
{
	protected function setUp() { 
		$emailTemplates = self::$event['emailTemplates'];
		$this->emailTemplate = current($emailTemplates);
		$this->logic = new logic_admin_emailTemplate_EditEmailTemplate();
	}
	
	public function testView() {
		$this->logic->view($this->emailTemplate['id']);
	}
	
	public function testSaveEmailTemplate() {
		$emailField = null;
		
		$fields = model_Event::getInformationFields(self::$event);
		foreach($fields as $field) {
			if($field['formInput']['id'] == model_FormInput::$TEXT) {
				$emailField = $field;		
			}
		}
		
		$template = array(
			'id' => $this->emailTemplate['id'],
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
		$this->logic->sendTestEmail($this->emailTemplate['id'], 'unit@test.ccc');
	}
}

?>