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
		
		$newRegNumber = count($this->group['registrations'])+1;
		
		$addregistrantRows = <<<_
			<tr>
				<td class="label">Category</td>
				<td style="padding-right:60px;">
					{$this->HTML->hidden(array(
						'class' => 'add-registrant-redirect',
						'value' => "/admin/registration/Registration?reportId={$this->report['id']}&groupId={$this->group['id']}#registrant{$newRegNumber}"
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
			'Continue'
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
			
			$sendEmailLink = '';
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
			
			// must cancel registration before you can delete it.
			$deleteLink = '';
			if(!empty($r['dateCancelled'])) {
				$deleteLink = $this->HTML->link(array(
					'label' => 'Permanantly Delete',
					'class' => 'delete-registrant',
					'title' => 'Permanantly delete this registrant',
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
						{$cancelLink} {$sendEmailLink} {$deleteLink}
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
}

?>