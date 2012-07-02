<?php

class fragment_editRegistrations_Registration extends template_Template
{
	private $event;
	private $group;
	private $registration;
	
	function __construct($event, $group, $registration, $registrantNum = 1) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $group;
		$this->registration = $registration;
		$this->registrantNum = $registrantNum;
	}
	
	public function html() {
		$html = '';
		
		$pages = model_EventPage::getVisiblePages($this->event, array('id' => $this->registration['categoryId']));
		foreach($pages as $page) {
			$fragment = new fragment_editRegistrations_Page($this->event, $page, $this->group, $this->registration, $this->registrantNum);
			
			$html .= $fragment->html();
		}
			
		return $html;
	}
}

?>