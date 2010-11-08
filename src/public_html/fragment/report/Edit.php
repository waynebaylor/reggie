<?php

class fragment_report_Edit extends template_Template
{
	private $report;
	
	function __construct($report) {
		parent::__construct();
		
		$this->report = $report;
	}
	
	public function html() {
		$form = new fragment_XhrTableForm(
			'/action/admin/report/Report',
			'saveReport',
			$this->getFormRows());
			
		return <<<_
			<div class="fragment-edit">
				<h3>Edit Report</h3>
				
				{$form->html()}
			</div>
_;
	}
	
	private function getFormRows() {
		return <<<_
			<tr>
				<td class="required label">Name</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $this->report['id']
					))}
					{$this->HTML->text(array(
						'name' => 'name',
						'value' => $this->escapeHtml($this->report['name']),
						'maxlength' => 255
					))}
				</td>
			</tr>
_;
	}
}

?>