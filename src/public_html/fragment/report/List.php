<?php

class fragment_report_List extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Reports</h3>
			
			<div class="fragment-list">
				<table class="admin">
					<tr>
						<th>Name</th>
						<th>Options</th>
					</tr>
					{$this->getReports()}
				</table>
			</div>
_;
	}
	
	private function getReports() {
		$html = '';
		
		$reports = $this->event['reports'];
		foreach($reports as $report) {
			$html .= <<<_
				<tr>
					<td>
						{$this->HTML->link(array(
							'label' => $report['name'],
							'href' => '/admin/report/GenerateReport',
							'title' => 'Generate Report',
							'parameters' => array(
								'a' => 'view',
								'id' => $report['id']
							)
						))}
						({$this->HTML->link(array(
							'label' => 'csv',
							'title' => 'Download report in CSV format',
							'href' => '/admin/report/GenerateReport',
							'parameters' => array(
								'a' => 'csv',
								'id' => $report['id']
							)
						))})
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/admin/report/Report',
							'parameters' => array(
								'action' => 'view',
								'id' => $report['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/admin/report/Report',
							'parameters' => array(
								'action' => 'removeReport',
								'id' => $report['id']
							),
							'class' => 'remove'
						))}
					</td>
				</tr>
_;
		}
		
		return $html;
	}
}

?>