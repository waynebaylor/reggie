<?php

class fragment_editRegistrations_Page extends template_Template
{
	private $page;
	private $report;
	private $group;
	private $registration;
	
	function __construct($page, $report, $group, $registration) {
		parent::__construct();
		
		$this->page = $page;
		$this->report = $report;
		$this->group = $group;
		$this->registration = $registration;
	}
	
	public function html() {
		$html = '';
			
		$sections = $this->page['sections'];
		foreach($sections as $section) {
			if(model_Section::containsRegTypes($section)) {
				$html .= $this->getRegTypeHtml($section, $this->registration);
			}
			else if(model_Section::containsContactFields($section)) {
				$fragment = new fragment_editRegistrations_InformationFields($section, $this->registration);
				$html .= $fragment->html();
			}
		}
			
		if(!empty($html)) {
			return <<<_
				<div class="fragment-edit">
					<h3>{$this->page['title']}</h3>
					
					{$html}
				</div>
				
				<div class="sub-divider"></div>
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
			{$html} ( <span class="change-reg-type-link link">Change</span> )
_;
	}
}

?>