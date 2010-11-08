<?php

class fragment_report_field_Fields extends template_Template
{
	private $event;
	private $report;
	
	function __construct($event, $report) {
		parent::__construct();
		
		$this->event = $event;
		$this->report = $report;
	}
	
	public function html() {
		$list = new fragment_report_field_List($this->report);
		$add = new fragment_report_field_Add($this->event, $this->report);
		
		return <<<_
			<div class="fragment-fields">
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