<?php

class fragment_report_Reports extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$list = new fragment_report_List($this->event);
		$add = new fragment_report_Add($this->event);
		
		return <<<_
			<div class="fragment-reports">
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