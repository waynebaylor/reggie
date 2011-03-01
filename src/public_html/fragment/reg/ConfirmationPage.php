<?php

class fragment_reg_ConfirmationPage extends template_Template
{
	private $event;
	private $regGroup;
	
	function __construct($event, $regGroup) {
		parent::__construct();
		
		$this->event = $event;
		$this->regGroup = $regGroup;
	}
	
	public function html() {
		$summary = new fragment_registration_summary_Summary($this->event, $this->regGroup);
		
		return <<<_
			<table class="confirmation">
				<tr>
					<td>{$this->event['confirmationText']}</td>
				</tr>
			</table>
			
			<div class="section-divider"></div>
			
			{$summary->html()}
_;
	}
}

?>