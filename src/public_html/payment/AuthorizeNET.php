<?php

class payment_AuthorizeNET
{
	private $logger;
	
	private $event;
	private $info;
	private $amount;
	
	private $url;
	
	function __construct($event, $info, $amount) {
		$this->logger = new Logger();
		
		$this->url = in_array(Config::$MODE_DEVELOPMENT, Config::$SETTINGS['MODE'])? 
							  Config::$SETTINGS['AUTH_NET_TEST_URL'] : Config::$SETTINGS['AUTH_NET_URL'];
						
		$this->event = $event;
		$this->amount = $amount;
		$this->info = $info;
	}
	
	public function makePayment() {
		$fields = $this->paymentFields();
		$response = $this->submitTransaction($fields);

		$this->logger->logPayment($this->event['code'].' Authorize.NET payment: '.$response);

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
	
	public function voidPayment() {
		
	}
	
	private function paymentFields() {
		$login = $this->event['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]['login'];
		$transactionKey = $this->event['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]['transactionKey'];
		
		$fields = array(
			'x_relay_response' => 'FALSE', // always FALSE for AIM
			'x_version' => '3.1',
			'x_type' => 'AUTH_CAPTURE',
			'x_method' => 'CC',
			'x_delim_data' => 'TRUE',
			'x_delim_char' => '|',
		
			'x_login' => $login,
			'x_tran_key' => $transactionKey,
			'x_amount' => $this->amount,
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
		
		return $fields;
	}
	
	private function submitTransaction($fields) {
		// escape the fields for use as URL parameters.
		$paramString = '';
		foreach($fields as $key => $value) {
			$paramString .= $key.'='.urlencode($value).'&';
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
}

?>