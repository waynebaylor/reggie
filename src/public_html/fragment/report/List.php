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
		$evenRow = true;
		
		$reports = $this->event['reports'];
		foreach($reports as $report) {
			$evenRow = !$evenRow;
			$rowClass = $evenRow? 'even' : 'odd';
			$html .= <<<_
				<tr class="{$rowClass}">
					<td>
						{$this->HTML->link(array(
							'label' => $report['name'],
							'href' => '/action/admin/report/RunReport',
							'title' => 'Generate Report',
							'target' => '_blank',
							'parameters' => array(
								'action' => 'view',
								'id' => $report['id']
							)
						))}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/action/admin/report/Report',
							'parameters' => array(
								'action' => 'view',
								'id' => $report['id'],
								'eventId' => $this->event['id']
							)
						))}
						
						{$this->HTML->link(array(
							'label' => 'Remove',
							'href' => '/action/admin/report/Report',
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