<?php

class fragment_report_field_Add extends template_Template
{
	private $event;
	private $report;
	
	function __construct($event, $report) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
	}
	
	public function html() {
		$form = new fragment_XhrAddForm(
			'Add Field',
			'/admin/report/ReportField',
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
						'name' => 'reportId',
						'value' => $this->report['id']
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
		
		// general registrant info comes first.
		$opts[] = array(
			'label' => 'General',
			'value' => array(
				array(
					'label' => 'Date Registered',
					'value' => 'date_registered'
				),
				array(
					'label' => 'Category',
					'value' => 'category'
				),
				array(
					'label' => 'Registration Type',
					'value' => 'registration_type'
				)
			)
		);
		
		// group all the information fields by section id.
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
		
		// payment fields come last.
		$opts[] = array(
			'label' => 'Payment Information',
			'value' => array(
				array(
					'label' => 'Total Cost',
					'value' => 'total_cost'
				),
				array(
					'label' => 'Total Paid',
					'value' => 'total_paid'
				),
				array(
					'label' => 'Remaining Balance',
					'value' => 'remaining_balance'
				)
			)
		);
		
		return $opts;
	}
}

?>