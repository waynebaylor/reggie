<?php

class fragment_contactField_ContactFields extends template_Template
{
	private $section;
	private $event;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->section = $section;
		$this->event = $event;
	}

	public function html() {
		$list = new fragment_contactField_List($this->event, $this->section);
		$add = new fragment_contactField_Add($this->event, $this->section);
		
		return <<<_
			<div class="fragment-contact-fields">
				<div>
					{$list->html()}
				</div>
				
				<div class="sub-divider"></div>
				
				{$add->html()}				
			</div>
_;
	}
}

?>