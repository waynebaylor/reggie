<?php

class fragment_reg_Page extends template_Template
{
	private $event;
	private $page;
	
	function __construct($event, $page) {
		parent::__construct();
		
		$this->event = $event;
		$this->page = $page;	
	}
	
	public function html() {
		$html = '';
		
		foreach($this->page['sections'] as $section) {
			$s = new fragment_reg_Section($this->event, $section);
			
			$html .= $s->html();
		}
		
		return $html;		
	}
}

?>