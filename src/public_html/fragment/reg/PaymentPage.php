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
			
			<div class="amount-due">
				Total Due: {$this->getTotalDue()}
			</div>

			{$this->getGroupRegistration()}
			
			<table class="payment-types">
				<tr>
					<td class="tab-cell">{$this->getPaymentTypeTabs()}</td>
					<td class="form-cell">{$this->getPaymentTypeForms()}</td>
				</tr>
			</table>
			
			<div class="section-divider"></div>
_;
	}
	
	private function getGroupRegistration() {
		$html = '';
		
		$value = self::$ADD_PERSON_ACTION;
		
		if($this->event['groupRegistration']['enabled'] === 'true') {
			$html = <<<_
				<div style="padding: 10px 0 0;">
					You may add another person to your group before entering payment information.
					<br/><br/>
					<input type="submit" class="button" name="a" value="{$value}"/>
				</div>
				
				<div class="divider"></div>
_;
		}
		
		return $html;
	}
	
	private function getPaymentTypeTabs() {
		$html = '<table class="payment-type-tabs">';
		
		$paymentInfo = model_RegSession::getPaymentInfo();
		$paymentTypeFromSession = $paymentInfo['paymentType']; 
		$selectedTypeId = empty($paymentTypeFromSession)? model_PaymentType::$AUTHORIZE_NET : intval($paymentTypeFromSession, 10);
		
		foreach($this->event['paymentTypes'] as $type) {
			$typeId = intval($type['paymentTypeId'], 10);
			
			$selected = $selectedTypeId === $typeId;
			
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
		$info = model_RegSession::getPaymentInfo();
		
		$showForm = 'hide';
		if(isset($info['paymentType']) && (model_PaymentType::$CHECK === intval($info['paymentType'], 10))) {
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
		$info = model_RegSession::getPaymentInfo();
		
		$showForm = 'hide';
		if(isset($info['paymentType']) && (model_PaymentType::$PO === intval($info['paymentType'], 10))) {
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
		$info = model_RegSession::getPaymentInfo();
		
		$showForm = 'hide';
		if(empty($info['paymentType']) || (model_PaymentType::$AUTHORIZE_NET === intval($info['paymentType'], 10))) {
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
		$total = model_Registration::getTotalCost($this->event);
		
		return '$'.$total;
	}
	
	private function getMonth() {
		$info = model_RegSession::getPaymentInfo();
		
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
		$info = model_RegSession::getPaymentInfo();
		
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
}

?>