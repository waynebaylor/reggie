<?php

class fragment_editRegistrations_Page extends template_Template
{
	private $event;
	private $page;
	private $group;
	private $registration;
	
	function __construct($event, $page, $group, $registration, $registrantNum = 1) {
		parent::__construct();
		
		$this->event = $event;
		$this->page = $page;
		$this->group = $group;
		$this->registration = $registration;
		$this->registrantNum = $registrantNum;
	}
	
	public function html() {
		$html = '';
			
		$sections = $this->page['sections'];
		foreach($sections as $section) {
			if(model_Section::containsRegTypes($section)) {
				$html .= $this->getRegTypeHtml($section, $this->registration);
				$fragmentClass = 'registrant-details-section';
			}
			else if(model_Section::containsContactFields($section)) {
				$fragment = new fragment_editRegistrations_InformationFields($section, $this->registration);
				$html .= $fragment->html();
				$fragmentClass = 'fragment-edit';
			}
		}
			
		// replace non alpha-numeric with _ and then lowercase everything.
		$subTabId = preg_replace('/[^0-9a-zA-Z_]/', '_', $this->page['title']);
		$subTabId = "registrant{$this->registration['id']}-".strtolower($subTabId);
		
		if(!empty($html)) {
			return <<<_
				<div id="{$subTabId}" class="registrant-sub-tab">
					<span class="hide sub-tab-label">{$this->page['title']}</span>
					
					<div class="{$fragmentClass}">
						<h3>{$this->page['title']}</h3>
						
						{$html}
					</div>
				</div>
_;
		}
		else {
			return '';
		}
	}

	private function getRegTypeHtml($section, $registration) {
		$regTypes = $section['content'];
		
		$html = '';
		
		foreach($regTypes as $regType) {
			if($registration['regTypeId'] === $regType['id']) {
				$html .= $regType['description'];
			}
		}
		
		return <<<_
			<div class="change-reg-type">
				{$html} ( <span class="change-reg-type-link link">Change</span> )
				
				<div class="change-reg-type-content hide">
					{$this->getChangeRegTypeForm($registration)}
				</div>
			</div>
_;
	}
	
	private function getChangeRegTypeForm($registration) {
		$items = array();
		foreach($this->event['regTypes'] as $regType) {
			$items[] = array(
				'label' => $regType['description'],
				'value' => $regType['id']
			);
		}
		
		// used to make url unique, so redirect with # is forced to actually reload the page.
		$timestamp = time();
		
		$rows = <<<_
			<tr>
				<td colspan="2">
					<span style="font-weight:bold;">WARNING:</span> Changing the registration type will cancel all registration options.
					<div class="sub-divider"></div>
				</td>
			</tr>
			<tr>
				<td class="label">Registration Type</td>
				<td>
					{$this->HTML->hidden(array(
						'class' => 'change-reg-type-redirect',
						'value' => "/admin/registration/Registration?eventId={$this->event['id']}&id={$registration['regGroupId']}&{$timestamp}#showTab=registrant{$registration['id']}&showSubTab=registrant{$registration['id']}-registration_type"
					))}
					
					{$this->HTML->hidden(array(
						'name' => 'registrationId',
						'value' => $registration['id']
					))}
					{$this->HTML->hidden(array(
						'name' => 'eventId',
						'value' => $this->event['id']
					))}
					
					{$this->HTML->select(array(
						'name' => 'regTypeId',
						'value' => $registration['regTypeId'],
						'items' => $items
					))}
				</td>
			</tr>	
_;

		$form = new fragment_XhrTableForm(
			'/admin/registration/Registration', 
			'changeRegType', 
			$rows,
			'Continue'
		);
		
		return <<<_
			{$form->html()}
_;
	}
}

?>