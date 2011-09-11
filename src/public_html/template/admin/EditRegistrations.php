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
				dojo.require("hhreg.admin.editRegistrations");
			</script>
			
			<div id="content">
				<h3>Edit Registrations</h3>
				
				<div class="add-registrant">
					<span class="add-registrant-link link">Add Registrant To Group</span>
					
					<div class="add-registrant-content hide">
						{$this->getAddRegistrantForm()}
					</div>
				</div>
				
				{$this->getRegistrants()}
				
				<div class="divider"></div>
			</div>
_;
	}
	
	private function getAddRegistrantForm() {
		$regTypeDropdown = fragment_regType_HTML::selectByEventId($this->event['id'], array(
			'name' => 'regTypeId', 'multiple' => false, 'size' => 1
		));
		
		$addregistrantRows = <<<_
			<tr>
				<td class="label required">Registration Type</td>
				<td style="padding-right:60px;">
					{$this->HTML->hidden(array(
						'name' => 'reportId',
						'value' => $this->report['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'regGroupId',
						'value' => $this->group['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					
					{$regTypeDropdown}
				</td>
			</tr>
_;

		$addRegistrantForm = new fragment_XhrTableForm(
			'/admin/registration/Registration', 
			'addRegistrantToGroup', 
			$addregistrantRows,
			'Continue',
			'There was a problem saving. Please try again.',
			false
		);
		
		return $addRegistrantForm->html();
	}
	
	private function getRegistrants() {
		$html = '';
		
		$registrations = $this->group['registrations'];
		foreach($registrations as $index => $r) {
			$num = $index+1;
			$numDisplayed = count($registrations) > 1? $num : '';

			$comments = new fragment_XhrTableForm(
				'/admin/registration/Registration', 
				'saveGeneralInfo', 
				$this->getRegistrantRow($r)
			);

			$html .= <<<_
				<div class="sub-divider"></div>
				<a name="registrant{$num}"></a>
				<div class="sub-divider"></div>
_;
			
			$regFragment = new fragment_editRegistrations_Registration($this->event, $this->report, $this->group, $r);
			$options = new fragment_editRegistrations_RegOptions($this->event, $this->report, $r);
			
			if(empty($r['dateCancelled'])) {
				$cancelLink = $this->HTML->link(array(
					'label' => 'Cancel',
					'class'	=> 'cancel-registrant',
					'title' => 'Cancel this registrant',
					'href'	=> '/admin/registration/Registration',
					'parameters' => array(
						'a' => 'cancelRegistration',
						'eventId' => $this->event['id'],
						'registrationId' => $r['id'],
						'reportId' => $this->report['id'],
						'registrantNumber' => $num
						
					)
				));
				$cancelDate = '';
				$cancelCss = '';
			}
			else {
				$cancelLink = '';
				$cancelDate = '<span style="font-weight:bold; color:red;">( Cancelled )</span>'; 
				$cancelCss = 'cancelled';
			}	
			
			$sendEmailLink = $this->HTML->link(array(
				'label' => 'Create/Enable Email Template',
				'title' => 'An email template must be enabled to send email confirmations',
				'href' => '/admin/emailTemplate/EmailTemplates',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->event['id']
				)
			));
			$emailTemplate = db_EmailTemplateManager::getInstance()->findByRegTypeId($this->event['id'], $r['regTypeId']);
			if(!empty($emailTemplate)) {
				$sendEmailLink = $this->HTML->link(array(
					'label' => 'Send Confirmation',
					'title' => 'Send email confirmation to this registrant',
					'href' => '/admin/registration/Registration',
					'parameters' => array(
						'a' => 'sendConfirmation',
						'eventId' => $this->event['id'],
						'registrationId' => $r['id'],
						'reportId' => $this->report['id']
					)
				));
			}
			
			$printBadgeLink = '';
			$printBadgeDialog = '';
			$badgeTemplates = db_BadgeTemplateManager::getInstance()->findByRegTypeId($this->event['id'], $r['regTypeId']);
			if(!empty($badgeTemplates) && empty($r['dateCancelled'])) {
				$printBadgeLink = '<span class="print-badge-link link" title="Print badge for this registrant">Print Badge</span>';
				$printBadgeDialog = $this->getPrintBadgeDialog($this->event['id'], $r['id'], $badgeTemplates);
			}
			
			// must cancel registration before you can delete it.
			$deleteLink = '';
			if(!empty($r['dateCancelled'])) {
				$deleteLink = $this->HTML->link(array(
					'label' => 'Permanently Delete',
					'class' => 'delete-registrant',
					'title' => 'Permanently delete this registrant',
					'href' => '/admin/registration/Registration',
					'parameters' => array(
						'a' => 'deleteRegistration',
						'eventId' => $this->event['id'],
						'registrationId' => $r['id'],
						'reportId' => $this->report['id']
					)
				));
			}
					
			$html .= <<<_
				<div class="registrant {$cancelCss}">
					<div class="registrant-heading">
						Registrant {$numDisplayed} {$cancelDate}
					</div>	
					<div class="registrant-links">
						{$sendEmailLink} {$printBadgeLink} {$cancelLink} {$deleteLink}
						
						{$printBadgeDialog}
					</div>
					
					<div class="sub-divider"></div>
					
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
		$registeredDate = substr($r['dateRegistered'], 0, 10);
		$confirmationNumber = model_Registrant::getConfirmationNumber($r);
		$leadNumber = model_Registrant::getLeadNumber($r);
		
		$cancelledRow = '';
		if(!empty($r['dateCancelled'])) {
			$cancelledDate = substr($r['dateCancelled'], 0, 10);
			$cancelledRow = <<<_
				<tr>
					<td class="label">Date Cancelled</td>
					<td>{$cancelledDate}</td>
				</tr>
_;
		}
		
		return <<<_
			<tr>
				<td class="label">Date Registered</td>
				<td>{$registeredDate}</td>
			</tr>
			<tr>
				<td class="label">Confirmation Number&nbsp;</td>
				<td>{$confirmationNumber}</td>
			</tr>
			<tr>
				<td class="label">Lead Number</td>
				<td>{$leadNumber}</td>
			</tr>
			{$cancelledRow}
			<tr>
				<td colspan="2">
					<div class="sub-divider"></div>
				</td>
			</tr>
			<tr>
				<td class="label">Comments</td>
				<td>
					{$this->HTML->hidden(array(
						'name' => 'id',
						'value' => $r['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
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
	
	private function getPrintBadgeDialog($eventId, $registrationId, $templates) {
		$items = array();
		foreach($templates as $template) {
			$items[] = array(
				'label' => $template['name'],
				'value' => $template['id']
			);
		}
		
		$templateSelect = $this->HTML->select(array(
			'name' => 'badgeTemplateId',
			'items' => $items
		));
		
		return <<<_
			<div class="print-badge-dialog hide">
				<form method="post" action="{$this->contextUrl('/admin/badge/PrintBadge')}" target="_blank">
					{$this->HTML->hidden(array(
						'name' => 'a',
						'value' => 'singleBadge'
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $eventId
					))}
					{$this->HTML->hidden(array(
						'name' => 'registrationId',
						'value' => $registrationId
					))}
					
					<table style="border-collapse:separate; border-spacing:10px;">
						<tr>
							<td class="label">Badge Template</td>
							<td>
								{$templateSelect}
							</td>
						</tr>
						<tr>
							<td class="label">Shift Right</td>
							<td>
								{$this->HTML->text(array(
									'name' => 'shiftRight',
									'value' => '0.0',
									'size' => 5
								))} in
							</td>
						</tr>
						<tr>
							<td class="label">Shift Down</td>
							<td>
								{$this->HTML->text(array(
									'name' => 'shiftDown',
									'value' => '0.0',
									'size' => 5
								))} in
							</td>
						</tr>
						<tr>
							<td></td>
							<td>
								<input type="button" class="print-badge-button" value="Print">
							</td>
						</tr>
					</table>
				</form>
			</div>
_;
	}
}

?>