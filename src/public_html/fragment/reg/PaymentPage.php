<?php

class fragment_reg_PaymentPage extends template_Template
{
	// text used on the group registration button for adding another person. access to this
	// property is needed by other resources since it's not a valid action value (it has spaces).
	public static $ADD_PERSON_ACTION = 'Add another person';
	
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		return <<<_
			

			{$this->getGroupRegistration()}
			
			<div class="general-payment-instructions">
				{$this->event['paymentInstructions']}
			</div>
			
			<div class="amount-due">
				Total Due: {$this->getTotalDue()}
			</div>
			
			<div id="general-errors"></div>

			{$this->getPaymentTypes()}
			
			<div class="section-divider"></div>
_;
	}
	
	private function getPaymentTypes() {
		$total = model_reg_Registration::getTotalCost($this->event);
		
		if($total > 0) {
			$types = new fragment_payment_PaymentChooser($this->event, model_reg_Session::getPaymentInfo());
			return $types->html();
		}
		else {
			return '<div>No payment due.</div>';
		}
	}
	
	private function getGroupRegistration() {
		$html = '';
		
		$value = self::$ADD_PERSON_ACTION;
		
		if($this->event['groupRegistration']['enabled'] === 'T') {
			$html = <<<_
				<div>
					You may add another person to your group before entering payment information.
					<br/><br/>
					<input type="submit" class="button" name="a" value="{$value}">
				</div>
_;
		}
		
		return $html;
	}
	
	private function getTotalDue() { 
		$total = model_reg_Registration::getTotalCost($this->event);
		
		return '$'.number_format($total, 2);
	}
}

?>