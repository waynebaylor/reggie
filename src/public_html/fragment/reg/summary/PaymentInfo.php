<?php

class fragment_reg_summary_PaymentInfo extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$cost = model_Registration::getTotalCost($this->event);
		
		if(!empty($this->event['paymentTypes']) && $cost > 0) {
			return <<<_
				<tr>
					<td class="label">Payment Information</td>
					<td class="details">
						<div id="general-errors"></span>
						{$this->getPaymentInfo()}
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<div class="summary-divider"></div>
					</td>
				</tr>	
_;
		}
		
		return '';
	}
	
	private function getPaymentInfo() {
		$rows = '';
		
		$info = model_RegSession::getPaymentInfo();
		
		switch($info['paymentType']) {
			case model_PaymentType::$CHECK:
				$rows .= <<<_
					<tr>
						<td class="info-field">Check Number:</td>
						<td> 
							{$this->escapeHtml($info['checkNumber'])}
						</td>
					</tr>
_;
				break;
			case model_PaymentType::$PO:
				$rows .= <<<_
					<tr>
						<td class="info-field">Purchase Order Number:</td>
						<td> 
							{$this->escapeHtml($info['purchaseOrderNumber'])}
						</td>
					</tr>
_;
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				$cc = substr($info['cardNumber'], -4);
				$rows .= <<<_
					<tr>
						<td class="info-field">Credit Card:</td>
						<td>*{$this->escapeHtml($cc)}</td>
					</tr>
					<tr>
						<td class="info-field">Expiration Date:</td>
						<td>
							{$this->escapeHtml($info['month'])}/{$this->escapeHtml($info['year'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">Name:</td>
						<td>
							{$this->escapeHtml($info['firstName'])} {$this->escapeHtml($info['lastName'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">Address:</td>
						<td>
							{$this->escapeHtml($info['address'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">City:</td>
						<td>
							{$this->escapeHtml($info['city'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">State:</td>
						<td>
							{$this->escapeHtml($info['state'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">Zip:</td>
						<td>
							{$this->escapeHtml($info['zip'])}
						</td>
					</tr>
					<tr>
						<td class="info-field">Country:</td>
						<td>
							{$this->escapeHtml($info['country'])}
						</td>
					</tr>
_;
				break;
			default:
				break;
		}
		
		return <<<_
			<table class="payment-info">
				{$rows}
			</table>
_;
	}
}

?>