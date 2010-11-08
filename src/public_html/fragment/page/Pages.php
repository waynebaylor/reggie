<?php

class fragment_page_Pages extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;	
	}
	
	public function html() {
		$list = new fragment_page_List($this->event);
		$add = new fragment_page_Add($this->event);

		return <<<_
			<div class="fragment-pages">
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