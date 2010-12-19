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
			<script type="text/javascript">
				dojo.require("hhreg.reg.paymentTypes");
			</script>

			{$this->getGroupRegistration()}
			
			<div class="amount-due">
				Total Due: {$this->getTotalDue()}
			</div>

			{$this->getPaymentTypes()}
			
			<div class="section-divider"></div>
_;
	}
	
	private function getPaymentTypes() {
		$total = model_reg_Registration::getTotalCost($this->event);
		
		if($total > 0) {
			return <<<_
				<table class="payment-types">
					<tr>
						<td class="tab-cell">{$this->getPaymentTypeTabs()}</td>
						<td class="form-cell">{$this->getPaymentTypeForms()}</td>
					</tr>
				</table>
_;
		}
		else {
			return <<<_
				<div>No payment due.</div>		
_;
		}
	}
	
	private function getGroupRegistration() {
		$html = '';
		
		$value = self::$ADD_PERSON_ACTION;
		
		if($this->event['groupRegistration']['enabled'] === 'true') {
			$html = <<<_
				<div>
					You may add another person to your group before entering payment information.
					<br/><br/>
					<input type="submit" class="button" name="a" value="{$value}"/>
				</div>
_;
		}
		
		return $html;
	}
	
	private function getPaymentTypeTabs() {
		$html = '<table class="payment-type-tabs">';
		
		$selectedTypeId = $this->getSelectedPaymentTypeId();
		
		foreach($this->event['paymentTypes'] as $type) {
			$typeId = intval($type['paymentTypeId'], 10);
			
			$selected = ($selectedTypeId === $typeId);
			
			if(model_PaymentType::$AUTHORIZE_NET === $typeId) {
				$type['displayName'] = 'Credit Card'; // We don't want attendees to see 'Authorize.NET'.
			}
			
			$tabSelected = $selected? 'selected-tab' : '';
			
			$html .= <<<_
				<tr>
					<td class="payment-type-tab {$tabSelected}">
						{$this->HTML->radio(array(
							'name' => 'paymentType',
							'value' => $type['paymentTypeId'],
							'label' => $type['displayName'],
							'checked' => $selected
						))}
					</td>
				</tr>
_;
		}
		
		return $html.'</table>';
	}
	
	private function getPaymentTypeForms() {
		$html = '';
		
		foreach($this->event['paymentTypes'] as $paymentType) {
			$html .= $this->getPaymentTypeForm($paymentType);
		}
		
		return <<<_
			<div class="payment-instructions">
				{$html}
				
				<div class="sub-divider"></div>
			</div>
_;
	}
	
	private function getPaymentTypeForm($type) {
		$html = '';
		
		switch($type['paymentTypeId']) {
			case model_PaymentType::$CHECK:
				$html .= $this->checkForm($type);
				break;
			case model_PaymentType::$PO:
				$html .= $this->purchaseOrderForm($type);
				break;
			case model_PaymentType::$AUTHORIZE_NET:
				$html .= $this->authorizeNetForm($type);
				break;
			default:
				throw new Exception('Invalid payment type: '.$type['paymentTypeId']);
		}

		return $html;
		
	}
	
	private function checkForm($type) {
		$info = model_reg_Session::getPaymentInfo();
		
		$showForm = 'hide';
		if(model_PaymentType::$CHECK === $this->getSelectedPaymentTypeId()) {
			$showForm = '';
		}
		
		return  <<<_
			<div class="check-payment-instructions {$showForm}">
				<div>{$type['instructions']}</div>

				<div class="sub-divider"></div>
				
				<div>
					Check Number
					{$this->HTML->text(array(
						'name' => 'checkNumber',
						'value' => isset($info['checkNumber'])? $this->escapeHtml($info['checkNumber']) : '',
						'size' => '10'
					))}
				</div>
			</div>
_;
	}
	
	private function purchaseOrderForm($type) {
		$info = model_reg_Session::getPaymentInfo();
		
		$showForm = 'hide';
		if(model_PaymentType::$PO === $this->getSelectedPaymentTypeId()) {
			$showForm = '';
		}
		
		return <<<_
			<div class="po-payment-instructions {$showForm}">
				<div>{$type['instructions']}</div>

				<div class="sub-divider"></div>
				
				<div>
					PO Number
					{$this->HTML->text(array(
						'name' => 'purchaseOrderNumber',
						'value' => isset($info['purchaseOrderNumber'])? $this->escapeHtml($info['purchaseOrderNumber']) : '',
						'size' => '10'
					))}
				</div>
			</div>
_;
	}
	
	private function authorizeNetForm($type) {
		$info = model_reg_Session::getPaymentInfo();
		
		$showForm = 'hide';
		if(model_PaymentType::$AUTHORIZE_NET === $this->getSelectedPaymentTypeId()) {
			$showForm = '';
		}
		
		return <<<_
			<div class="authorizeNet-payment-instructions {$showForm}">
				<div>{$type['instructions']}</div>
				<table class="auth-net-form">
					<tr>
						<td colspan="2" class="credit-card-title">
							Credit Card Information
						</td>
					</tr>
					<tr>
						<td class="label">Card Number</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'cardNumber',
								'value' => isset($info['cardNumber'])? $this->escapeHtml($info['cardNumber']) : '',
								'size' => '16',
								'maxlength' => '16'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Expiration Date</td>
						<td>
							{$this->getMonth()}
							{$this->getYear()}
						</td>
					</tr>
					<tr>
						<td colspan="2" class="billing-title">Billing Information</td>
					</tr>
					<tr>
						<td class="label">First Name</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'firstName',
								'value' => isset($info['firstName'])? $this->escapeHtml($info['firstName']) : '',
								'size' => '15'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Last Name</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'lastName',
								'value' => isset($info['lastName'])? $this->escapeHtml($info['lastName']) : '',
								'size' => '15'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Address</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'address',
								'value' => isset($info['address'])? $this->escapeHtml($info['address']) : '',
								'size' => '20'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">City</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'city',
								'value' => isset($info['city'])? $this->escapeHtml($info['city']) : '',
								'size' => '20'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">State</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'state',
								'value' => isset($info['state'])? $this->escapeHtml($info['state']) : '',
								'size' => '10'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Zip</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'zip',
								'value' => isset($info['zip'])? $this->escapeHtml($info['zip']) : '',
								'size' => '10'
							))}
						</td>
					</tr>
					<tr>
						<td class="label">Country</td>
						<td>
							{$this->HTML->text(array(
								'name' => 'country',
								'value' => isset($info['country'])? $this->escapeHtml($info['country']) : 'US',
								'size' => '10'
							))}
						</td>
					</tr>
				</table>
			</div>
_;
	}
	
	private function getTotalDue() { 
		$total = model_reg_Registration::getTotalCost($this->event);
		
		return '$'.number_format($total, 2);
	}
	
	private function getMonth() {
		$info = model_reg_Session::getPaymentInfo();
		
		return $this->HTML->select(array(
			'name' => 'month',
			'value' => isset($info['month'])? $this->escapeHtml($info['month']) : '01',
			'items' => array(
				array('label' => '01', 'value' => '01'),
				array('label' => '02', 'value' => '02'),
				array('label' => '03', 'value' => '03'),
				array('label' => '04', 'value' => '04'),
				array('label' => '05', 'value' => '05'),
				array('label' => '06', 'value' => '06'),
				array('label' => '07', 'value' => '07'),
				array('label' => '08', 'value' => '08'),
				array('label' => '09', 'value' => '09'),
				array('label' => '10', 'value' => '10'),
				array('label' => '11', 'value' => '11'),
				array('label' => '12', 'value' => '12')
			)
		));
	}
	
	private function getYear() {
		$info = model_reg_Session::getPaymentInfo();
		
		$currentYear = date('Y');
		$years = array();
		for($i=0; $i<15; $i++) {
			$y = intval($currentYear, 10) + $i;
			$years[] = array(
				'label' => strval($y),
				'value' => strval($y)
			);
		}
		
		return $this->HTML->select(array(
			'name' => 'year',
			'value' => isset($info['year'])? $this->escapeHtml($info['year']) : $currentYear,
			'items' => $years
		));
	}
	
	private function getSelectedPaymentTypeId() {
		$paymentInfo = model_reg_Session::getPaymentInfo();
		$paymentTypeFromSession = $paymentInfo['paymentType']; 
		
		$firstPaymentType = current($this->event['paymentTypes']);
		$firstPaymentType = $firstPaymentType['paymentTypeId'];

		// if no payment type has been selected, then default to the first one.
		$id = empty($paymentTypeFromSession)? $firstPaymentType : $paymentTypeFromSession;
		
		return intval($id, 10);
	}
}

?>