<?php

class template_admin_ReportResults extends template_AdminPage
{
	private $report;
	private $fieldHeadings;
	private $results;
	
	function __construct($report, $fieldHeadings, $results) {
		parent::__construct('Report Results');
		
		$this->report = $report;
		$this->fieldHeadings = $fieldHeadings;
		$this->results = $results;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
	}
	
	protected function getContent() {
		return <<<_
			<div id="content">
				<table class="admin">
					<tr>
						<th>Date Registered</th>
						<th>Category</th>
						<th>Registration Type</th>
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
					<td>{$result['dateRegistered']}</td>
					<td>{$result['categoryName']}</td>
					<td>{$result['regTypeName']}</td>
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
			else {
				$value = $result['fieldValues'][$heading['id']];
			}

			$html .= '<td>'.$value.'</td>';
		}
		
		return $html;
	}
}

?>