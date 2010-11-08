<?php

class fragment_sectionRegOptionGroup_RegOptionGroups extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		$list = new fragment_sectionRegOptionGroup_List($this->event, $this->section);
		$add = new fragment_sectionRegOptionGroup_Add($this->event, $this->section);
		
		return <<<_
			<div class="fragment-reg-option-groups">
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