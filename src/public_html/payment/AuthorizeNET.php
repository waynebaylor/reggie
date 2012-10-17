<?php

/**
 * 
 * For info on the x_line_item field see: http://developer.authorize.net/guides/AIM/Transaction_Data_Requirements/Itemized_Order_Information.htm
 * 
 * @author wtaylor
 *
 */
class payment_AuthorizeNET
{
	private $event;
	private $info;
	private $amount;
	private $regGroupId;
	
	private $url;
	
	// regGroupId is only used if isAdminPayment is TRUE.
	function __construct($event, $info, $amount, $isAdminPayment = FALSE, $regGroupId = 0) {
		$this->url = in_array(Config::$MODE_DEVELOPMENT, Config::$SETTINGS['MODE'])? 
							  Config::$SETTINGS['AUTH_NET_TEST_URL'] : Config::$SETTINGS['AUTH_NET_URL'];
						
		$this->event = $event;
		$this->amount = $amount;
		$this->info = $info;
		$this->regGroupId = $regGroupId;
		
		// if this is an admin payment, then we don't set the special values for payment description
		// and payment line items.
		$this->isAdminPayment = $isAdminPayment;
	}
	
	private function avsCheck() {
		$fields = $this->paymentFields('AUTH_ONLY', 0.00);
		$response = $this->submitTransaction($fields);

		Logger::logPayment($this->event['code'].' Authorize.NET AVS check: '.$response);

		// break the response up into an array.
		$response = explode('|', $response);
		
		$response = array(
			// AIM Response Code 1 means approved.
			// Response Reason Code 289 means $0 authorization not allowed. 
			// AVS check is considered successful if the response is approved or
			// if $0 AVS checks are not allowed--in this case we must go ahead with
			// the full transaction.
			'success' => (intval($response[0], 10) === 1 || intval($response[2], 10) === 289), 
			'responseText' => $response[3]
		);
		
		return $response;
	}
	
	public function makePayment() {
		$fields = $this->paymentFields('AUTH_CAPTURE', $this->amount);
		$response = $this->submitTransaction($fields);

		Logger::logPayment($this->event['code'].' Authorize.NET payment: '.$response);

		// break the response up into an array.
		$response = explode('|', $response);
		
		return array(
			'success' => (intval($response[0], 10) === 1), // AIM response code 1 means approved.
			'responseText' => $response[3],
			'authorizationCode' => $response[4],
			'transactionId' => $response[6],
			'cardSuffix' => substr($response[50], -4),
			'cardType' => $response[51]
		);
	}
	
	private function paymentFields($type, $amount) {
		$login = $this->event['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]['login'];
		$transactionKey = $this->event['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]['transactionKey'];
		
		$fields = array(
			'x_relay_response' => 'FALSE', // always FALSE for AIM
			'x_version' => '3.1',
			'x_type' => $type,
			'x_method' => 'CC',
			'x_delim_data' => 'TRUE',
			'x_delim_char' => '|',
		
			'x_login' => $login,
			'x_tran_key' => $transactionKey,
			'x_invoice_num' => substr("{$this->event['code']} Registration", 0, 20),
			'x_description' => "{$this->event['code']} Registration",
			'x_amount' => $amount,
			'x_card_num' => $this->info['cardNumber'],
			'x_exp_date' => $this->info['month'].$this->info['year'],
			'x_first_name' => $this->info['firstName'],
			'x_last_name' => $this->info['lastName'],
			'x_address' => $this->info['address'],
			'x_city' => $this->info['city'],
			'x_state' => $this->info['state'],
			'x_zip' => $this->info['zip'],
			'x_country' => ArrayUtil::getValue($this->info, 'country', 'US')
		);
		
		// non-admin AVA payment.
		if($type === 'AUTH_CAPTURE' && !$this->isAdminPayment && in_array($this->event['id'], array(10, 17, 18))) {
			$lineItems = $this->getLineItems();
			$authNetLineItems = $this->getAuthNetLineItems($lineItems);
			
			$fields['x_line_item'] = $authNetLineItems;	
			
			// overwrite description.
			$fields['x_description'] = substr($this->getAVADescription(), 0, 255);
		}
		// admin AVA payment (only works if this is the first payment for the group and it covers the total balance due).
		else if($type === 'AUTH_CAPTURE' && $this->isAdminPayment && in_array($this->event['id'], array(10, 17, 18))) {
			$lineItems = $this->getAdminLineItems();
			
			$balanceDue = 0.00;
			foreach($lineItems as $item) {
				$balanceDue += $item['quantity']*$item['unitPrice'];
			}
			
			if(!empty($lineItems) && ($balanceDue == $amount)) {
				$authNetLineItems = $this->getAuthNetLineItems($lineItems);
				
				$fields['x_line_item'] = $authNetLineItems;
				
				// overwrite description.
				$desc = substr($this->getAVAAdminDescription(), 0, 255);
				$fields['x_description'] = $desc;
				
			}
		}

		return $fields;
	}
	
	/**
	 * NOTE: this has a dependency on the attendee registration session object.
	 */
	private function getAVADescription() {
		$lineItems = array();
		
		$registrations = model_reg_Registration::getConvertedRegistrationsFromSession();
		foreach($registrations as $reg) {
			$regLineItems = $this->getRegistrationLineItems($this->event, $reg);
			$lineItems = array_merge($lineItems, $regLineItems);
		}
		
		$desc = $this->getLineItemDescription($lineItems);
		
		return $desc;
	}
	
	private function getLineItemDescription($lineItems) {
		$desc = array();
		
		foreach($lineItems as $item) {
			$cost = $item['quantity']*$item['unitPrice'];
			// don't show zero-dollar options and variable quantity options with zero quantity.
			if($cost > 0) {
				$desc[] = "{$item['code']}: {$cost}";
			}
		}
		
		return implode('; ', $desc);
	}
	
	/**
	 * NOTE: this has a dependency on the attendee registration session object.
	 */
	private function getLineItems() {
		$lineItems = array();
		
		$registrations = model_reg_Registration::getConvertedRegistrationsFromSession();
		foreach($registrations as $reg) {
			$regLineItems = $this->getRegistrationLineItems($this->event, $reg);
			$lineItems = array_merge($lineItems, $regLineItems);
		}
		
		return $lineItems;
	}
	
	/**
	 * @param array $lineItems [ [id, code, description, quantity, price] ]
	 */
	private function getAuthNetLineItems($lineItems) {
		// auth.net will only accept 30 line items.
		$lineItemCount = count($lineItems);
		if($lineItemCount > 30) {
			$lineItems = array_slice($lineItems, 0, 29);
			$lineItems[] = $this->getSanitizedLineItem(
				0, 
				'ATTENTION', 
				"Showing 29 of {$lineItemCount} line items.", 
				1, 
				0.00
			);
		}
		
		// convert to auth.net "<|>" delimited strings.
		$authNetLineItems = array();
		foreach($lineItems as $item) {
			$authNetLineItems[] = implode('<|>', $item);
		}
		
		return $authNetLineItems;
	}
	
	private function getRegistrationLineItems($event, $registration) {
		$lineItems = array();

		$regOptLineItems = $this->getRegOptionLineItems($event, $registration);
		$lineItems = array_merge($lineItems, $regOptLineItems);
		
		$varQuantOptLineItems = $this->getVarQuantityOptionLineItems($event, $registration);
		$lineItems = array_merge($lineItems, $varQuantOptLineItems);
		
		return $lineItems;
	}
	
	private function getRegOptionLineItems($event, $registration) {
		$lineItems = array();
		
		$regOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($regOptionGroups as $regOptGroup) {
			$regOptGroupLineItems = $this->getRegOptionGroupLineItems($registration, $regOptGroup);
			$lineItems = array_merge($lineItems, $regOptGroupLineItems);
		}
		
		return $lineItems;
	}
	
	private function getVarQuantityOptionLineItems($event, $registration) {
		$lineItems = array();
		
		$variableQuantityOptions = model_Event::getVariableQuantityOptions($event);
		foreach($variableQuantityOptions as $varQuantOpt) {
			foreach($registration['variableQuantity'] as $v) {
				if($v['id'] == $varQuantOpt['id']) {
					$price = model_RegOption::getPrice(
						array('id' => $registration['regTypeId']), 
						$varQuantOpt
					);
					
					$line = $this->getSanitizedLineItem(
						$varQuantOpt['id'], 
						$varQuantOpt['code'], 
						$varQuantOpt['description'], 
						$v['quantity'], 
						$price['price']
					);
					
					$lineItems[] = $line;
				}
			}
		}
		
		return $lineItems;
	}
	
	private function getRegOptionGroupLineItems($registration, $regOptGroup) {
		$lineItems = array();
		
		foreach($regOptGroup['options'] as $regOption) {			
			if(in_array($regOption['id'], $registration['regOptionIds'])) {
				$price = model_RegOption::getPrice(
					array('id' => $registration['regTypeId']), 
					$regOption
				);

				if(!empty($price)) {
					$line = $this->getSanitizedLineItem(
						$regOption['id'], 
						$regOption['code'], 
						$regOption['description'], 
						1, 
						$price['price']
					);
					
					$lineItems[] = $line;					
				}
				
				foreach($regOption['groups'] as $subGroup) {
					$subGroupLineItems = $this->getRegOptionGroupLineItems($registration, $subGroup);
					$lineItems = array_merge($lineItems, $subGroupLineItems);
				}
			}	
		}
		
		return $lineItems;
	}
	
	private function getSanitizedLineItem($id, $name, $desc, $quantity, $price) {
		$disallowedCharsPattern = '/[^0-9a-zA-Z.]/'; // only allow 0-9, a-z, A-Z, and period.
		$extraWhitespacePattern = '/\s+/';
		
		$id = preg_replace($disallowedCharsPattern, ' ', $id);
		$name = preg_replace($disallowedCharsPattern, ' ', $name);
		$desc = preg_replace($disallowedCharsPattern, ' ', $desc);
		$quantity = preg_replace($disallowedCharsPattern, ' ', $quantity);
		$price = preg_replace($disallowedCharsPattern, ' ', $price);
		
		$id = preg_replace($extraWhitespacePattern, ' ', $id);
		$name = preg_replace($extraWhitespacePattern, ' ', $name);
		$desc = preg_replace($extraWhitespacePattern, ' ', $desc);
		$quantity = preg_replace($extraWhitespacePattern, ' ', $quantity);
		$price = preg_replace($extraWhitespacePattern, ' ', $price);
		
		$id = substr($id, 0, 31);
		$name = substr($name, 0, 31);
		$desc = substr($desc, 0, 255);
			
		// put into array and add the final 'is taxable' field.
		$line = array(
			'id' => $id, 
			'code' => $name, 
			'description' => $desc, 
			'quantity' => $quantity, 
			'unitPrice' => $price, 
			'taxable' => 'NO');
			
		return $line;
	}
	
	private function submitTransaction($fields) {
		// escape the fields for use as URL parameters.
		$paramString = '';
		foreach($fields as $key => $value) {
			if(is_array($value)) {
				foreach($value as $val) {
					$paramString .= $key.'='.urlencode($val).'&';
				}	
			}
			else {
				$paramString .= $key.'='.urlencode($value).'&';
			}
		}
		$paramString = rtrim($paramString, '&');

		// submit the data to authorize.net.
		$transaction = curl_init($this->url);
		
		curl_setopt($transaction, CURLOPT_HEADER, false); 
		curl_setopt($transaction, CURLOPT_RETURNTRANSFER, true); 
		curl_setopt($transaction, CURLOPT_POST, true);
		curl_setopt($transaction, CURLOPT_POSTFIELDS, $paramString);
		//curl_setopt($transaction, CURLOPT_SSL_VERIFYPEER, FALSE); // uncommenting this will tell cURL not to verify host's certificate.
		
		$response = curl_exec($transaction); 
		curl_close($transaction);
		
		return $response;
	}
	
	private function getAdminLineItems() {
		$lineItems = array();
		
		$data = db_reg_PaymentManager::getInstance()->findAdminPaymentData($this->regGroupId);
		foreach($data as $d) {
			$line = $this->getSanitizedLineItem(
				$d['id'], 
				$d['code'], 
				$d['description'], 
				$d['quantity'], 
				$d['price']
			);
			
			$lineItems[] = $line;
		}	
		
		return $lineItems;
	}
	
	private function getAVAAdminDescription() {
		$lineItems = $this->getAdminLineItems();
		
		$desc = $this->getLineItemDescription($lineItems);
		
		return $desc;
	}
}

?>