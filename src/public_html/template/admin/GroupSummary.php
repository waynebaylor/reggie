<?php

class template_admin_GroupSummary extends template_AdminPage
{
	private $event;
	private $report;
	private $group;
	
	function __construct($event, $report, $group) {
		parent::__construct('Summary');
		
		$this->event = $event;
		$this->report = $report;
		$this->group = $group;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'GroupSummary',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code'],
			'reportName' => $this->report['name'],
			'reportId' => $this->report['id']
		));
	}
	
	protected function getContent() {
		$f = new fragment_registration_summary_Summary($this->event, $this->group);
		return <<<_
			<div id="content">
				<h3>Group Summary</h3>
				<div>
					{$this->HTML->link(array(
						'label' => 'Details',
						'href' => '/admin/registration/Registration',
						'parameters' => array(
							'groupId' => $this->group['id'],
							'reportId' => $this->report['id']
						)
					))}
				</div>
				
				<div class="sub-divider"></div>
				
				{$f->html()}
			</div>
_;
	}
}

?>