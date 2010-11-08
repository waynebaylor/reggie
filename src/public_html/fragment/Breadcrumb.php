<?php

class fragment_Breadcrumb extends template_Template
{
	public $SEPARATOR = ' &gt; ';
	
	private $contactFieldManager;
	private $sectionManager;
	private $pageManager;
	private $regTypeManager;
	private $optionGroupManager;
	private $sectionGroupManager;
	private $regOptionManager;
	private $regOptionPriceManager;
	
	private $config;
	
	function __construct($config) {
		parent::__construct();
		
		$this->contactFieldManager = db_ContactFieldManager::getInstance();
		$this->sectionManager = db_PageSectionManager::getInstance();
		$this->pageManager = db_PageManager::getInstance();
		$this->regTypeManager = db_RegTypeManager::getInstance();
		$this->optionGroupManager = db_RegOptionGroupManager::getInstance();
		$this->sectionGroupManager = db_SectionRegOptionGroupManager::getInstance();
		$this->regOptionManager = db_RegOptionManager::getInstance();
			
		$this->config = $config;
	}
	
	public function html() {
		$html = '';
		
		switch($this->config['location']) {
			case 'Reports':
				$html = $this->reports();
				$html .= $this->SEPARATOR;
				$html .= "{$this->config['event']['code']} Reports";
				break;
			case 'FileUpload':
				$html = $this->fileUpload();
				$html .= $this->SEPARATOR;
				$html .= "{$this->config['event']['code']} Files";
				break;
			case 'EmailTemplate':
				$html = $this->emailTemplate();
				$html .= $this->SEPARATOR;
				$html .= 'Email Template';
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
				break;
			case 'RegOptionPrice':
				$html = $this->regOptionPrice($this->config['id']);
				break;
			case 'RegOption':
				$html = $this->regOption($this->config['id']);
				break;
			case 'OptionGroup':
				$html = $this->optionGroup($this->config['id'], $this->config['isSectionGroup']);
				break;
			case 'RegType':
				$html = $this->regType($this->config['id']);
				break;
			case 'ContactField':
				$html = $this->contactField($this->config['id']);
				break;
			case 'Section':
				$html = $this->section($this->config['id']);
				break;
			case 'Page':
				$html = $this->page($this->config['id']);
				break;
			case 'Event':
				$html = $this->event();
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
				'label' => 'Events',
				'href' => '/action/MainMenu',
				'parameters' => array(
					'action' => 'view'
				)
			))}
_;
	}
	
	private function page($id) {
		$page = $this->pageManager->find($id);
		
		return <<<_
			{$this->event($page['eventId'])}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Event',
				'href' => '/action/admin/event/EditEvent',
				'parameters' => array(
					'action' => 'view',
					'id' => $page['eventId']
				)
			))}
_;
	}
	
	private function section($id) {
		$section = $this->sectionManager->find($id);
		
		return <<<_
			{$this->page($section['pageId'])}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Page',
				'href' => '/action/admin/page/Page',
				'parameters' => array(
					'action' => 'view',
					'id' => $section['pageId'],
					'eventId' => $this->config['eventId']
				)
			))}
_;
	}
	
	private function contactField($id) {
		$field = $this->contactFieldManager->find($id);
		 
		return <<<_
			{$this->section($field['sectionId'])}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/action/admin/section/Section',
				'parameters' => array(
					'action' => 'view',
					'id' => $field['sectionId'],
					'eventId' => $this->config['eventId']
				)
			))}		
_;
	}
	
	private function regType($id) {
		$type = $this->regTypeManager->find($id);
		
		return <<<_
			{$this->section($type['sectionId'])}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/action/admin/section/Section',
				'parameters' => array(
					'action' => 'view',
					'id' => $type['sectionId'],
					'eventId' => $this->config['eventId']
				)
			))}	
_;
	}
	
	private function optionGroup($id, $isSectionGroup) {
		if($isSectionGroup === true) {
			$group = $this->sectionGroupManager->find($id);
			
			return <<<_
				{$this->section($group['sectionId'])}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Section',
					'href' => '/action/admin/section/Section',
					'parameters' => array(
						'action' => 'view',
						'id' => $group['sectionId'],
						'eventId' => $this->config['eventId']
					)
				))}
_;
		}
		else {
			$group = $this->optionGroupManager->find($id);
			
			return <<<_
				{$this->regOption($group['regOptionId'])}
				{$this->SEPARATOR}
				{$this->HTML->link(array(
					'label' => 'Reg Option',
					'href' => '/action/admin/regOption/RegOption',
					'parameters' => array(
						'action' => 'view',
						'id' => $group['regOptionId'],
						'eventId' => $this->config['eventId']
					)
				))}		
_;
		}
	}
	
	private function regOption($id) {
		$option = $this->regOptionManager->find($id);
		
		$group = $this->sectionGroupManager->find($option['parentGroupId']);
		if(empty($group)) {
			$group = $this->optionGroupManager->find($option['parentGroupId']);
		}
		
		$action = model_Group::isSectionGroup($group)? 
			'/action/admin/regOption/SectionRegOptionGroup' : 
			'/action/admin/regOption/RegOptionGroup';
	
		return <<<_
			{$this->optionGroup($option['parentGroupId'], model_Group::isSectionGroup($group))}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Option Group',
				'href' => $action,
				'parameters' => array(
					'action' => 'view',
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
					'href' => '/action/admin/regOption/VariableQuantity',
					'parameters' => array(
						'action' => 'view',
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
					'href' => '/action/admin/regOption/RegOption',
					'parameters' => array(
						'action' => 'view',
						'id' => $price['regOptionId'],
						'eventId' => $this->config['eventId']
					)
				))}
_;
		}
	}
	
	private function variableQuantityOption($id) {
		$option = db_VariableQuantityOptionManager::getInstance()->find($id);
		
		return <<<_
			{$this->section($option['sectionId'])}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => 'Section',
				'href' => '/action/admin/section/Section',
				'parameters' => array(
					'action' => 'view',
					'id' => $option['sectionId'],
					'eventId' => $this->config['eventId']
				)
			))}		
_;
	}
	
	private function report() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "{$this->config['event']['code']} Reports",
				'href' => '/action/admin/report/Report',
				'parameters' => array(
					'action' => 'eventReports',
					'id' => $this->config['event']['id']
				)
			))}	
_;
	}
	
	private function appearance() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Edit {$this->config['event']['code']}",
				'href' => '/action/admin/event/EditEvent',
				'parameters' => array(
					'action' => 'view',
					'id' => $this->config['event']['id']
				)
			))}	
_;
	}
	
	private function paymentOptions() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Edit {$this->config['event']['code']}",
				'href' => '/action/admin/event/EditEvent',
				'parameters' => array(
					'action' => 'view',
					'id' => $this->config['event']['id']
				)
			))}	
_;
	}
	
	private function emailTemplate() {
		return <<<_
			{$this->event()}
			{$this->SEPARATOR}
			{$this->HTML->link(array(
				'label' => "Edit {$this->config['event']['code']}",
				'href' => '/action/admin/event/EditEvent',
				'parameters' => array(
					'action' => 'view',
					'id' => $this->config['event']['id']
				)
			))}	
_;
	}
	
	private function fileUpload() {
		return <<<_
			{$this->HTML->link(array(
				'label' => 'Events',
				'href' => '/action/MainMenu',
				'parameters' => array(
					'action' => 'view'
				)
			))}
_;
	}
	
	private function reports() {
		return <<<_
			{$this->HTML->link(array(
				'label' => 'Events',
				'href' => '/action/MainMenu',
				'parameters' => array(
					'action' => 'view'
				)
			))}
_;
	}
}

?>