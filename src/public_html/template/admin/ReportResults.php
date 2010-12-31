<?php

class template_admin_ReportResults extends template_AdminPage
{
	private $event;
	private $report;
	private $fieldHeadings;
	private $results;
	
	function __construct($event, $report, $fieldHeadings, $results) {
		parent::__construct($report['name'].' Report Results');
		
		$this->event = $event;
		$this->report = $report;
		$this->fieldHeadings = $fieldHeadings;
		$this->results = $results;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'ReportResults',
			'eventCode' => $this->event['code'],
			'eventId' => $this->event['id'],
			'reportName' => $this->report['name']
		));
	}
	
	protected function getContent() {
		return <<<_
			<div id="content">
				<h3>{$this->report['name']}</h3>
				
				<table class="admin">
					<tr>
						{$this->getFieldHeadings()}
					</tr>
					{$this->getResultRows()}
				</table>
			</div>
_;
	}
	
	private function getFieldHeadings() {
		$html = '';
		
		foreach($this->fieldHeadings as $heading) {
			$html .= '<th>'.$heading['displayName'].'</th>';
		}
		
		return $html;
	}
	
	private function getResultRows() {
		$html = '';
		
		foreach($this->results as $result) {
			$html .= <<<_
				<tr>
					{$this->getFieldValues($result)}
				</tr>
_;
		}
		
		return $html;
	}
	
	private function getFieldValues($result) {	
		$html = '';
		
		foreach($this->fieldHeadings as $heading) {
			if(empty($result['fieldValues'][$heading['id']])) {
				$value = '';
			}
			else if(is_array($result['fieldValues'][$heading['id']])) {
				$value = implode(', ', $result['fieldValues'][$heading['id']]);
			}
			else if($heading['id'] === 'details') {
				$value = $this->HTML->link(array(
					'label' => 'Details',
					'href' => '/admin/registration/Registration',
					'parameters' => array(
						'groupId' => $result['fieldValues'][$heading['id']]
					)
				));
			}
			else {
				$value = $result['fieldValues'][$heading['id']];
			}

			$html .= '<td>'.$value.'</td>';
		}
		
		return $html;
	}
}

?>