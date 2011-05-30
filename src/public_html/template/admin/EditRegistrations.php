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
		$categories = model_Category::values();
		$category = reset($categories);
		$categoryDropDown = fragment_category_HTML::radios(array(
			'name' => 'categoryId',
			'value' => $category['id']
		));
		
		$addregistrantRows = <<<_
			<tr>
				<td class="label">Category</td>
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
					
					{$categoryDropDown}
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
						'registrationId' => $r['id'],
						'reportId' => $this->report['id']
					)
				));
			}
			
			$printBadgeLink = '';
			$badgeTemplates = db_BadgeTemplateManager::getInstance()->findByRegTypeId($this->event['id'], $r['regTypeId']);
			if(!empty($badgeTemplates)) {
				$printBadgeLink = '<span class="print-badge-link link" title="Print badge for this registrant">Print Badge</span>';
			}
			
			$printBadgeDialog = $this->getPrintBadgeDialog($this->event['id'], $r['id'], $badgeTemplates);
			
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
					
					<span class="label">Badge Template</span>
					{$templateSelect}
					<br><br>
					<input type="button" class="print-badge-button" value="Print">
				</form>
			</div>
_;
	}
}

?>