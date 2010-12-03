<?php

class fragment_reg_Page extends template_Template
{
	private $page;
	
	function __construct($page) {
		parent::__construct();
		
		$this->page = $page;	
	}
	
	public function html() {
		$html = '';
		
		foreach($this->page['sections'] as $section) {
			$s = new fragment_reg_Section($section);
			
			$html .= $s->html();
		}
		
		return $html;		
	}
}

?>