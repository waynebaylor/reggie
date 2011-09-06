<?php

class fragment_Breadcrumb extends template_Template
{
	public $SEPARATOR = ' &gt; ';
	
	private $contactFieldManager;
	private $sectionManager;
	private $pageManager;
	private $regTypeManager;
	private $regOptionManager;
	private $regOptionPriceManager;
	
	private $config;
	
	function __construct($config) {
		parent::__construct();
		
		$this->contactFieldManager = db_ContactFieldManager::getInstance();
		$this->sectionManager = db_PageSectionManager::getInstance();
		$this->pageManager = db_PageManager::getInstance();
		$this->regTypeManager = db_RegTypeManager::getInstance();
		$this->regOptionManager = db_RegOptionManager::getInstance();
			
		$this->config = $config;
	}
	
	public function html() {
		$html = '';
		
		switch($this->config['location']) {
			case 'EditBadgeTemplate':
				$html = $this->editBadgeTemplate();
				$html .= $this->SEPARATOR;
				$html .= 'Edit Badge Template';
				break;
			case 'BadgeTemplates':
				$html = $this->badgeTemplates();
				$html .= $this->SEPARATOR;
				$html .= 'Badge Templates';
				break;
			case 'EditStaticPage':
				$html = $this->editStaticPage();
				$html .= $this->SEPARATOR;
				$html .= 'Edit Page Content';
				break;
			case 'StaticPageList':
				$html = $this->staticPageList();
				$html .= $this->SEPARATOR;
				$html .= "Event Pages ({$this->config['eventCode']})";
				break;
			case 'EditEmailTemplate':
				$html = $this->editEmailTemplate();
				$html .= $this->SEPARATOR;
				$html .= 'Edit Email Template';
				break;
			case 'GroupSummary':
				$html = $this->groupSummary();
				$html .= $this->SEPARATOR;
				$html .= 'Group Summary';
				break;
			case 'EditPayment':
				$html = $this->editPayment();
				$html .= $this->SEPARATOR;
				$html .= 'Edit Payment';
				break;
			case 'EditRegistrations':
				$html = $this->editRegistrations();
				$html .= $this->SEPARATOR;
				$html .= 'Edit Registrations';
				break;
			case 'GenerateReport':
				$html = $this->reportResults();
				$html .= $this->SEPARATOR;
				$html .= "Report Results ({$this->config['reportName']})";
				break;
			case 'GroupRegistration':
				$html = $this->groupReg();
				$html .= $this->SEPARATOR;
				$html .= 'Group Registration';
				break;
			case 'User':
				$html = $this->event();
				$html .= $this->SEPARATOR;
				$html .= 'Users';
				break;
			case 'Reports':
				$html = $this->reports();
				$html .= $this->SEPARATOR;
				$html .= "Reports ({$this->config['eventCode']})";
				break;
			case 'FileUpload':
				$html = $this->fileUpload();
				$html .= $this->SEPARATOR;
				$html .= "Files ({$this->config['eventCode']})";
				break;
			case 'EmailTemplates':
				$html = $this->emailTemplate();
				$html .= $this->SEPARATOR;
				$html .= 'Email Templates';
				break;
			case 'PaymentOptions':
				$html = $this->paymentOptions();
				$html .= $this->SEPARATOR;
				$html .= 'Payment Options';
				break;
			case 'Appearance':
				$html = $this->appearance();
				$html .= $this->SEPARATOR;
				$html .= 'Appearance';
				break;
			case 'Report':
				$html = $this->report();
				$html .= $this->SEPARATOR;
				$html .= 'Report';
				break;
			case 'VariableQuantityOption':
				$html = $this->variableQuantityOption($this->config['id']);
				$html .= $this->SEPARATOR;
				$html .= 'Variable Quantity Option';
				break;
			case 'RegOptionPrice':
				$html = $this->regOptionPrice($this->config['id']);
				$html .= $this->SEPARATOR;
				$html .= 'Reg Option Price';
				break;
			case 'RegOption':
				$html = $this->regOption($this->config['id']);
				$html .= $this->SEPARATOR;
				$html .= 'Reg Option';
				break;
			case 'OptionGroup':
				$html = $this->optionGroup($this->config['id'], $this->config['isSectionGroup']);
				$html .= $this->SEPARATOR;
				$html .= 'Option Group';
				break;
			case 'RegType':
				$this->config = db_BreadcrumbManager::getInstance()->findRegTypeCrumbs($this->config['regTypeId']);
				$html = $this->regType();
				$html .= $this->SEPARATOR;
				$html .= 'Reg Type';
				break;
			case 'ContactField':
				$this->config = db_BreadcrumbManager::getInstance()->findContactFieldCrumbs($this->config['contactFieldId']);
				$html = $this->contactField();
				$html .= $this->SEPARATOR;
				$html .= 'Information Field';
				break;
			case 'Section':
				$this->config = db_BreadcrumbManager::getInstance()->findSectionCrumbs($this->config['sectionId']);
				$html = $this->section();
				$html .= $this->SEPARATOR;
				$html .= 'Section';
				break;
			case 'Page':
				$html = $this->page();
				$html .= $this->SEPARATOR;
				$html .= 'Page';
				break;
			case 'Event':
				$html = $this->event();
				$html .= $this->SEPARATOR;
				$html .= "Event ({$this->config['event']['code']})";
				break;
		}
		
		return <<<_
			<div class="breadcrumb">
				{$html}
			</div>
_;
	}
	
	private function event() {
		return <<<_
			{$this->HTML->link(array(
				'label' => 'Main Menu',
				'href' => '/admin/Login',
				'parameters' => array(
					'a' => 'view'
				)
			))}
_;
	}
	
	private function page() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event ({$this->config['eventCode']})",
				'href' => '/admin/event/EditEvent',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}
_;
	}
	
	private function section() {
		return <<<_
			{$this->page()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Page',
				'href' => '/admin/page/Page',
				'parameters' => array(
					'a' => 'view',
					'id' => $this->config['pageId'],
					'eventId' => $this->config['eventId']
				)
			))}
_;
	}
	
	private function contactField() {
		return <<<_
			{$this->section()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/admin/section/Section',
				'parameters' => array(
					'a' => 'view',
					'id' => $this->config['sectionId']
				)
			))}		
_;
	}
	
	private function regType() {
		return <<<_
			{$this->section()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/admin/section/Section',
				'parameters' => array(
					'a' => 'view',
					'id' => $this->config['sectionId']
				)
			))}	
_;
	}
	
	private function optionGroup($id, $isSectionGroup) {
		if($isSectionGroup === true) {
			$group = db_GroupManager::getInstance()->find($id);
			
			$this->config = db_BreadcrumbManager::getInstance()->findSectionCrumbs($group['sectionId']);
			
			return <<<_
				{$this->section()}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Section',
					'href' => '/admin/section/Section',
					'parameters' => array(
						'a' => 'view',
						'id' => $this->config['sectionId']
					)
				))}
_;
		}
		else {
			$group = db_GroupManager::getInstance()->find($id);
			
			return <<<_
				{$this->regOption($group['regOptionId'])}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Reg Option',
					'href' => '/admin/regOption/RegOption',
					'parameters' => array(
						'a' => 'view',
						'id' => $group['regOptionId'],
						'eventId' => $this->config['eventId']
					)
				))}		
_;
		}
	}
	
	private function regOption($id) {
		$option = $this->regOptionManager->find($id);
		
		$group = db_GroupManager::getInstance()->find($option['parentGroupId']);
		
		$action = model_RegOptionGroup::isSectionGroup($group)? 
			'/admin/regOption/SectionRegOptionGroup' : 
			'/admin/regOption/RegOptionGroup';
	
		return <<<_
			{$this->optionGroup($option['parentGroupId'], model_RegOptionGroup::isSectionGroup($group))}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Option Group',
				'href' => $action,
				'parameters' => array(
					'a' => 'view',
					'id' => $option['parentGroupId'],
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function regOptionPrice($id) {
		$price = db_RegOptionPriceManager::getInstance()->find($id);
		
		if(db_RegOptionPriceManager::getInstance()->isVariableQuantityPrice($price)) {
			return <<<_
				{$this->variableQuantityOption($price['regOptionId'])}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Variable Quantity Option',
					'href' => '/admin/regOption/VariableQuantity',
					'parameters' => array(
						'a' => 'view',
						'id' => $price['regOptionId'],
						'eventId' => $this->config['eventId']
					)
				))}		
_;
		}
		else {
			return <<<_
				{$this->regOption($price['regOptionId'])}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Reg Option',
					'href' => '/admin/regOption/RegOption',
					'parameters' => array(
						'a' => 'view',
						'id' => $price['regOptionId'],
						'eventId' => $this->config['eventId']
					)
				))}
_;
		}
	}
	
	private function variableQuantityOption($id) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($id);
		
		$this->config = db_BreadcrumbManager::getInstance()->findSectionCrumbs($option['sectionId']);
		return <<<_
			{$this->section()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/admin/section/Section',
				'parameters' => array(
					'a' => 'view',
					'id' => $this->config['sectionId']
				)
			))}		
_;
	}
	
	private function report() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Reports ({$this->config['eventCode']})",
				'href' => '/admin/report/ReportList',
				'parameters' => array(
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function appearance() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event ({$this->config['eventCode']})",
				'href' => '/admin/event/EditEvent',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function paymentOptions() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event ({$this->config['eventCode']})",
				'href' => '/admin/event/EditEvent',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function emailTemplate() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event ({$this->config['eventCode']})",
				'href' => '/admin/event/EditEvent',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function fileUpload() {
		return $this->event();
	}
	
	private function reports() {
		return $this->event();
	}
	
	private function groupReg() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event ({$this->config['eventCode']})",
				'href' => '/admin/event/EditEvent',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function reportResults() {
		return <<<_
			{$this->reports()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Reports ({$this->config['eventCode']})",
				'href' => '/admin/report/ReportList',
				'parameters' => array(
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function editRegistrations() {
		return <<<_
			{$this->reportResults()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Report Results ({$this->config['reportName']})",
				'href' => '/admin/report/GenerateReport',
				'parameters' => array(
					'eventId' => $this->config['eventId'],
					'reportId' => $this->config['reportId']
				)
			))}	
_;
	}
	
	private function editPayment() {
		return <<<_
			{$this->editRegistrations()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Edit Registrations',
				'href' => '/admin/registration/Registration',
				'parameters' => array(
					'a' => 'view',
					'groupId' => $this->config['groupId'],
					'reportId' => $this->config['reportId']
				)
			))}	
_;
	}
	
	private function groupSummary() {
		return <<<_
			{$this->reportResults()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Report Results ({$this->config['reportName']})",
				'href' => '/admin/report/GenerateReport',
				'parameters' => array(
					'eventId' => $this->config['eventId'],
					'reportId' => $this->config['reportId']
				)
			))}
_;
	}
	
	private function editEmailTemplate() {
		return <<<_
			{$this->emailTemplate()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Email Templates',
				'href' => '/admin/emailTemplate/EmailTemplates',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function staticPageList() {
		return $this->event();
	}
	
	private function editStaticPage() {
		return <<<_
			{$this->staticPageList()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Event Pages ({$this->config['eventCode']})",
				'href' => '/admin/staticPage/PageList',
				'parameters' => array(
					'a' => 'view',
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function badgeTemplates() {
		return $this->event();
	}
	
	private function editBadgeTemplate() {
		return <<<_
		{$this->badgeTemplates()}
		{$this->SEPARATOR}
		{$this->HTML->link(array(
			'label' => 'Badge Templates',
			'href' => '/admin/badge/BadgeTemplates',
			'parameters' => array(
				'a' => 'view',
				'eventId' => $this->config['eventId']
			)
		))}
_;
	}
}

?>