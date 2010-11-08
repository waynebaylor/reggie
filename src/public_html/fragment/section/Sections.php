<?php

class fragment_section_Sections extends template_Template
{
	private $page;
	
	function __construct($page) {
		$this->page = $page;
	}
	
	public function html() {
		$list = new fragment_section_List($this->page);
		$add = new fragment_section_Add($this->page);
		
		return <<<_
			<div class="fragment-sections">
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