<?php

class logic_admin_emailTemplate_EmailTemplatesTest extends logic_admin_emailTemplate_Base
{
	protected function setUp() {
		$this->logic = new logic_admin_emailTemplate_EmailTemplates();
	}
	
	public function testView() {
		$this->logic->view(self::$event['id']);
	}
	
	public function testAddEmailTemplate() {
		$emailTemplate = current(self::$event['emailTemplates']);
		$fieldId = $emailTemplate['contactFieldId'];
		
		$this->logic->addEmailTemplate(array(
			'eventId' => self::$event['id'],
			'enabled' => 'F',
			'contactFieldId' => $fieldId,
			'fromAddress' => 'somethig@where.com',
			'bcc' => 'bcc@email.com',
			'subject' => 'The Subject',
			'header' => 'some header text',
			'footer' => 'some footer text'
		), array(-1));
	}
	
	public function testRemoveEmailTemplate() {
		$emailTemplate = current(self::$event['emailTemplates']);
		$this->logic->removeEmailTemplate($emailTemplate['id']);
	}
}

?>