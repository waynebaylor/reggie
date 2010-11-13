<?php

class fragment_report_field_List extends template_Template
{
	private $report;
	
	function __construct($report) {
		parent::__construct();
		
		$this->report = $report;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Fields</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th></th>
						<th>Name</th>
						<th>Options</th>
					</tr>
					{$this->getFields()}
				</table>
			</div>
_;
	}
	
	private function getFields() {
		$html = '';
		
		if($this->report['showDateRegistered'] === 'true') {
			$html .= <<<_
				<tr>
					<td></td>
					<td>Date Registered</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/report/ReportField',
							'parameters' => array(
								'action' => 'removeField',
								'id' => 'date_registered'
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		if($this->report['showCategory'] === 'true') {
			$html .= <<<_
				<tr>
					<td></td>
					<td>Category</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/report/ReportField',
							'parameters' => array(
								'action' => 'removeField',
								'id' => 'category'
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		if($this->report['showRegType'] === 'true') {
			$html .= <<<_
				<tr>
					<td></td>
					<td>Reg Type</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/report/ReportField',
							'parameters' => array(
								'action' => 'removeField',
								'id' => 'registration_type'
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		$fields = $this->report['fields'];
		foreach($fields as $field) {
			$arrows = new fragment_Arrows(array(
				'href' => '/action/admin/report/ReportField',
				'parameters' => array(
					'reportId' => $this->report['id']
				),
				'up' => array(
					'action' => 'moveFieldUp',
					'id' => $field['id']
				),
				'down' => array(
					'action' => 'moveFieldDown',
					'id' => $field['id']
				)
			));
			
			$html .= <<<_
				<tr>
					<td>
						{$arrows->html()}
					</td>
					<td>{$field['displayName']}</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/report/ReportField',
							'parameters' => array(
								'action' => 'removeField',
								'id' => $field['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		// FIXME: payment info fields here
		
		return $html;
	}
}

?>