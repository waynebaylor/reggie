<?php

class fragment_registration_summary_Summary extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $regGroup) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $regGroup;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->group['registrations'] as $index => $registration) {
			$regFragment = new fragment_registration_summary_Individual($this->event, $registration, $index);
			$html .= $regFragment->html();
		}
		
		$payments = new fragment_registration_summary_Payments($this->event, $this->group);
		$html .= $payments->html();
		
		return <<<_
			 <div class="registrant-details-section">
				{$html}
			</div>
_;
	}
}

?>