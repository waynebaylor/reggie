<?php

class fragment_editRegistrations_InformationFields extends template_Template
{
	private $section;
	private $registration;
	
	function __construct($section, $registration) {
		parent::__construct();
		
		$this->section = $section;
		$this->registration = $registration;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/admin/registration/Registration', 
			'save', 
			"<tr>
				<td></td>
				<td>
				{$this->HTML->hidden(array(
					'name' => 'registrationId',
					'value' => $this->registration['id']
				))}
				
				{$this->HTML->hidden(array(
					'name' => 'sectionId',
					'value' => $this->section['id']
				))}
				
				{$this->getInformationHtml($this->section, $this->registration)}
				</td>
			</tr>"
		);
		
		return $form->html();
	}
	
	private function getInformationHtml($section, $registration) {
		$regTypeId = $registration['regTypeId'];
		
		$values = array();
		foreach($registration['information'] as $info) {
			$values[$info['contactFieldId']] = $info['value'];
		}
		
		$fragment = new fragment_reg_ContactFields($section, $regTypeId, $values);

		return $fragment->html();
	}
}

?>