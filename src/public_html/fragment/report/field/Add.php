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
			'/action/admin/report/ReportField',
			'addField',
			$this->getFormRows());
			
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
		
		$fields = model_Event::getInformationFields($this->event);
		foreach($fields as $field) {
			$opts[] = array(
				'label' => $field['displayName'],
				'value' => $field['id']
			);
		}
		
		return $opts;
	}
}

?>