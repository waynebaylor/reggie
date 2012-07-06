<?php

class fragment_registration_summary_Summary extends template_Template
{
	private $event;
	private $group;
	
	function __construct($event, $regGroup, $isPdfFormat = FALSE) {
		parent::__construct();
		
		$this->event = $event;
		$this->group = $regGroup;
		$this->isPdfFormat = $isPdfFormat;
	}
	
	public function html() {
		$html = '';
		
		$multipleRegistrations = count($this->group['registrations']) > 1;
		
		foreach($this->group['registrations'] as $index => $registration) {
			// don't display a number if there is only one registrant. index will be < 0 if there is only one.
			$num = $multipleRegistrations? $index + 1 : '';
		
			$regFragment = new fragment_registration_summary_Individual($this->event, $registration, $num, $this->isPdfFormat);
			
			if($this->isPdfFormat) {
				$html .= '<br><br>';	
			}

			$html .= $regFragment->html();
		}
		
		$payments = new fragment_registration_summary_Payments($this->event, $this->group, $this->isPdfFormat);
		$html .= $payments->html();

		if($this->isPdfFormat) {
			$eventHeading = <<<_
				<table>
					<tr><td style="font-weight:bold; font-size:2em; text-align:center;">
						{$this->event['displayName']}	
					</td></tr>
				</table>
_;

			return $eventHeading.$html;
		}
		else {
			return <<<_
				 <div class="registrant-details-section">
					{$html}
				</div>
_;
		}
	}
}

?>