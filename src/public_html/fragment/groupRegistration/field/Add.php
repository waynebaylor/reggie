<?php

class fragment_groupRegistration_field_Add extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Field', 
			'/admin/event/EditGroupRegistration', 
			'addField', 
			$this->getFormRows()
		);
		
		return <<<_
			<div class="fragment-add">
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Field</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'groupRegistrationId',
						'value' => $this->event['groupRegistration']['id']
					))}
					
					{$this->HTML->select(array(
						'name' => 'contactFieldId',
						'value' => '',
						'items' => $this->getFields()
					))}
				</td>
			</tr>
_;
	}
	
	private function getFields() {
		$opts = array();
		
		$sectionFields = array();
		
		$fields = model_Event::getInformationFields($this->event);
		foreach($fields as $field) {
			$section = model_Event::getSectionById($this->event, $field['sectionId']);

			if(empty($sectionFields[$section['id']])) {
				$sectionFields[$section['id']] = array();
			}
			
			$sectionFields[$section['id']][] = array(
				'label' => $field['displayName'],
				'value' => $field['id']
			);
		}
		
		foreach($sectionFields as $sectionId => $fields) {
			$section = model_Event::getSectionById($this->event, $sectionId);
			$opts[] = array(
				'label' => $section['name'],
				'value' => $fields
			);
		}
		
		return $opts;
	}
}

?>
