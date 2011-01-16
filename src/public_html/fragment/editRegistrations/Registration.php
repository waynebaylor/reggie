<?php

class fragment_editRegistrations_Registration extends template_Template
{
	private $event;
	private $report;
	private $group;
	private $registration;
	
	function __construct($event, $report, $group, $registration) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
		$this->group = $group;
		$this->registration = $registration;
	}
	
	public function html() {
		$html = '';
		
		$pages = model_EventPage::getVisiblePages($this->event, array('id' => $this->registration['categoryId']));
		foreach($pages as $page) {
			$fragment = new fragment_editRegistrations_Page($page, $this->report, $this->group, $this->registration);
			
			$html .= $fragment->html();
		}
			
		return $html;
	}
}

?>