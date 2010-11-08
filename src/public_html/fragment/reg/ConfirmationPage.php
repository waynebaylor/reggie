<?php

class fragment_reg_ConfirmationPage extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$summary = new fragment_reg_summary_SummaryPage($this->event);
		
		return <<<_
			<table class="confirmation">
				<tr>
					<td>Thank you for registering!</td>
				</tr>
			</table>
			
			<div class="section-divider"></div>
			
			{$summary->html()}
_;
	}
}

?>