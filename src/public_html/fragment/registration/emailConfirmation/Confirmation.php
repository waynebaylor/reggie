<?php

class fragment_registration_emailConfirmation_Confirmation extends template_Template
{
	private $event;
	private $regGroup;
	
	function __construct($event, $regGroup) {
		parent::__construct();
		
		$this->event = $event;
		$this->regGroup = $regGroup;
	}
	
	public function html() {
		$html = '';
		
		$multipleRegistrations = count($this->regGroup['registrations']) > 1;
		
		foreach($this->regGroup['registrations'] as $index => $registration) {
			// don't display a number if there is only one registrant. index will be < 0 if there is only one.
			$num = $multipleRegistrations? $index + 1 : '';
			
			$frag = new fragment_registration_emailConfirmation_Individual($this->event, $registration, $num);
			$html .= $frag->html();
		}
		
		$payments = new fragment_registration_emailConfirmation_Payments($this->event, $this->regGroup);
		$html .= $payments->html();
		
		return <<<_
			<div style="font-family:sans-serif;">
				{$html}
			</div>	
_;
		
		return $html;
	}
}

?>