<?php

class template_admin_EditRegistrations extends template_AdminPage
{
	private $event;
	private $report;
	private $group;
	
	function __construct($event, $report, $group) {
		parent::__construct('Edit Registrations');
		
		$this->event = $event;
		$this->report = $report;
		$this->group = $group;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'EditRegistrations',
			'eventId' => $this->event['id'],
			'eventCode' => $this->event['code'],
			'reportName' => $this->report['name'],
			'reportId' => $this->report['id']
		));
	}
	
	protected function getContent() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				<h3>Edit Registrations</h3>
				
				{$this->getRegistrants()}
			</div>
_;
	}
	
	private function getRegistrants() {
		$html = '';
		
		$registrations = $this->group['registrations'];
		foreach($registrations as $index => $r) {
			$num = (count($registrations) > 1)? $index+1 : '';

			$comments = new fragment_XhrTableForm(
				'/admin/registration/Registration', 
				'saveGeneralInfo', 
				"
					<tr>
					<td class=\"label\">Comments</td>
					<td>
						{$this->HTML->hidden(array(
							'name' => 'id',
							'value' => $r['id']
						))}
						
						{$this->HTML->textarea(array(
							'name' => 'comments',
							'value' => $this->escapeHtml($r['comments']),
							'rows' => 5,
							'cols' => 50
						))}
					</td>
				"
			);
			
			$html .= <<<_
				<div style="background-color:#ccc; padding:5px; margin-bottom:10px; font-size:1.2em;">
					Registrant {$num}
				</div>	

				<div class="fragment-edit">
					<h3>General Registrant Information</h3>
					
					{$comments->html()}
				</div>
				
				<div class="sub-divider"></div>
_;

			$fragment = new fragment_editRegistrations_Registration($this->event, $this->group, $r);
			$html .= $fragment->html();
		}
		
		return $html;
	}
}

?>