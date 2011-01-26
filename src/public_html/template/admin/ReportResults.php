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
				<script type="text/javascript">
					dojo.require("hhreg.xhrEditForm");
					dojo.require("hhreg.admin.reportResults");
				</script>
			
				<h3>{$this->report['name']}</h3>
				
				<span id="create-reg-link" class="link">Create New Registration</span>
				<div id="create-reg-content" class="hide">
					{$this->getCreateRegForm()}
				</div>
				
				<div class="sub-divider"></div>
				
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
				$value = <<<_
					{$this->HTML->link(array(
						'label' => 'Details',
						'href' => '/admin/registration/Registration',
						'parameters' => array(
							'groupId' => $result['fieldValues'][$heading['id']],
							'reportId' => $this->report['id']
						)
					))}
					
					{$this->HTML->link(array(
						'label' => 'Summary',
						'href' => '/admin/registration/Summary',
						'parameters' => array(
							'regGroupId' => $result['fieldValues'][$heading['id']],
							'reportId' => $this->report['id']
						)
					))}
_;
			}
			else {
				$value = $result['fieldValues'][$heading['id']];
			}

			$html .= '<td>'.$value.'</td>';
		}
		
		return $html;
	}
	
	private function getCreateRegForm() {
		$categoryItems = array();
		foreach(model_Category::values() as $cat) {
			$categoryItems[] = array(
				'label' => $cat['displayName'],
				'value' => $cat['id']
			);
		}
		
		$rows = <<<_
			<tr>
				<td colspan="2">
					The new registration will show up as a blank row. 
					<br/>
					Click the 'Details' link to select a registration type
					<br/>
					and complete the registrant's information.
					
					<div class="sub-divider"></div>
				</td>
			</tr>
			<tr>
				<td class="label">Category</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'reportId',
						'value' => $this->report['id']
					))}
					
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					
					{$this->HTML->hidden(array(
						'id' => 'create-reg-redirect',
						'value' => "/admin/report/RunReport?id={$this->report['id']}"
					))}
					
					{$this->HTML->radios(array(
						'name' => 'categoryId',
						'items' => $categoryItems
					))}
				</td>
			</tr>
_;

		$form = new fragment_XhrTableForm(
			'/admin/registration/Registration', 
			'createNewRegistration', 
			$rows,
			'Continue'
		);
		
		return $form->html();
	}
}

?>