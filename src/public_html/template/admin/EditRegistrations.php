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
				dojo.require("hhreg.admin.editRegistrations");
			</script>
			
			<div id="content">
				<h3>Edit Registrations</h3>
				
				{$this->getRegistrants()}
				
				<div class="divider"></div>
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
				$this->getRegistrantRow($r)
			);

			if($index > 0) {
				$html .= '<div class="divider"></div>';	
			}
			
			$regFragment = new fragment_editRegistrations_Registration($this->event, $this->report, $this->group, $r);
			$options = new fragment_editRegistrations_RegOptions($this->event, $this->report, $r);
			
			if(empty($r['dateCancelled'])) {
				$cancelLink = $this->HTML->link(array(
					'label' => 'Cancel',
					'class'	=> 'cancel-registrant',
					'href'	=> '/admin/registration/Registration',
					'parameters' => array(
						'a' => 'cancelRegistration',
						'registrationId' => $r['id'],
						'reportId' => $this->report['id']
						
					)
				));
				$cancelLink = "({$cancelLink})";
				
				$cancelCss = '';
			}
			else {
				$cancelLink = '( Cancelled '.substr($r['dateCancelled'], 0, 10).' )';
					
				$cancelCss = 'cancelled';
			}	
					
			$html .= <<<_
				<div class="registrant {$cancelCss}">
					<div class="registrant-heading">
						Registrant {$num} {$cancelLink}
					</div>	
	
					<div class="fragment-edit">
						<h3>General Registrant Information</h3>
						
						{$comments->html()}
					</div>
					
					<div class="sub-divider"></div>
						
					{$regFragment->html()}
					
					{$options->html()}
				</div>
_;
		}
		
		$payments = new fragment_editRegistrations_payment_Payments($this->event, $this->report, $this->group);
		
		return <<<_
			{$html}
			
			{$payments->html()}
_;
	}
	
	private function getRegistrantRow($r) {
		return <<<_
			<tr>
				<td class="label">Comments</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $r['id']
					))}
					
					{$this->HTML->textarea(array(
						'name' => 'comments',
						'value' => $this->escapeHtml($r['comments']),
						'rows' => 5,
						'cols' => 75
					))}
				</td>
			</tr>
_;
	}
}

?>