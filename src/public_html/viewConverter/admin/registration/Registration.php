<?php

class viewConverter_admin_registration_Registration extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Registrations';
	}
	
	protected function body() {
		$body = parent::body();
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.admin.editRegistrations");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				<h3>Edit Registrations</h3>
				
				<div class="add-registrant">
					{$this->HTML->link(array(
						'label' => 'Add Registrant To Group',
						'href' => '/admin/registration/Registration',
						'parameters' => array(
							'a' => 'addRegistrantToGroup',
							'eventId' => $this->event['id'],
							'regGroupId' => $this->group['id']
						)
					))}
				</div>
				
				{$this->getRegistrants()}
				
				<div class="divider"></div>
			</div>		
_;

		return $body;
	}
	
	public function getSaveGeneralInfo($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getSave($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getChangeRegType($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
	
	public function getSendConfirmation($properties) {
		$this->setProperties($properties);
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->regGroupId}");
	}
	
	public function getDeleteRegistration($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->regGroupId}");
	}
	
	public function getPaymentSummary($properties) {
		$this->setProperties($properties);
		
		$frag = <<<_
			<tr>
				<td>Total Cost</td>
				<td>{$this->cost}</td>
			</tr>
			<tr>
				<td>Amount Tendered</td>
				<td>{$this->paid}</td>
			</tr>
			<tr>
				<td>Balance Due</td>
				<td>{$this->remainingBalance}</td>
			</tr>	
_;

		return new template_TemplateWrapper($frag);
	}
	
	public function getAddRegistrantToGroup($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->groupId}#registrant{$this->newNumber}");		
	}
	
	public function getCancelRegistration($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->regGroupId}#registrant{$this->registrantNumber}");
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
			
			$regFragment = new fragment_editRegistrations_Registration($this->event, $this->group, $r);
			$options = new fragment_editRegistrations_RegOptions($this->event, $r);
			
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
						'registrationId' => $r['id']
					)
				));
			}
			
			$printBadgeLink = '';
			$printBadgeDialog = '';
			$badgeTemplates = db_BadgeTemplateManager::getInstance()->findByRegTypeId(array(
				'eventId' => $this->event['id'], 
				'regTypeId' => $r['regTypeId']
			));
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
						'registrationId' => $r['id']
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
		
		$payments = new fragment_editRegistrations_payment_Payments($this->event, $this->group);
		
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