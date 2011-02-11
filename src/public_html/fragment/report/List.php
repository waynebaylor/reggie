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
		
		// special sample report for MM2011.
		if($this->event['id'] == 4) {
			$html = <<<_
				<tr>
					<td>
						{$this->HTML->link(array(
							'label' => 'Payments To Date',
							'href' => '/admin/report/RunReport',
							'parameters' => array(
								'a' => 'view',
								'id' => 'payments_to_date',
								'eventId' => $this->event['id']
							)
						))}
					</td>
					<td></td>
				</tr>
_;
		}
		
		$reports = $this->event['reports'];
		foreach($reports as $report) {
			$html .= <<<_
				<tr>
					<td>
						{$this->HTML->link(array(
							'label' => $report['name'],
							'href' => '/admin/report/RunReport',
							'title' => 'Generate Report',
							'parameters' => array(
								'action' => 'view',
								'id' => $report['id']
							)
						))}
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