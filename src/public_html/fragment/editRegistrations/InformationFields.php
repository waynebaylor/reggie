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
		$information = $this->getInformationHtml($this->section, $this->registration);
		
		if(!empty($information)) {
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
						
						{$information}
					</td>
				</tr>"
			);
			
			return $form->html();
		}
		
		return '';
	}
	
	private function getInformationHtml($section, $registration) {
		$regTypeId = $registration['regTypeId'];
		
		// see if the section has any fields this reg type can see.
		$sectionHasVisibleFields = false;
		foreach($section['content'] as $field) {
			if(model_ContactField::isVisibleTo($field, array('id' => $regTypeId))) {
				$sectionHasVisibleFields = true;
				break;
			}
		}
		
		if($sectionHasVisibleFields) {
			$values = array();
			foreach($registration['information'] as $info) {
				$values[$info['contactFieldId']] = $info['value'];
			}
			
			$fragment = new fragment_reg_ContactFields($section, $regTypeId, $values);
	
			return $fragment->html();
		}
		
		return '';
	}
}

?>