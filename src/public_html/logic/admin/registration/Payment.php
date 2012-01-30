<?php

class logic_admin_registration_Payment extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), $params['id']);
		
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $payment['regGroupId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'payment' => $payment,
			'group' => $group,
			'breadcrumbsParams' => array(
				'altEventId' => $params['eventId'], // don't want 'Event' link showing up
				'regGroupId' => $group['id'],
				'paymentId' => $payment['id']
			)
		);
	}
	
	public function savePayment($params) {
		db_reg_PaymentManager::getInstance()->save($params);
		
		return $params;
	}
	
	public function addPayment($params) {
		$group = $this->strictFindById(db_reg_GroupManager::getInstance(), $params['regGroupId']);
		$event = $this->strictFindById(db_EventManager::getInstance(), $params['eventId']);
		
		if($params['paymentType'] == model_PaymentType::$AUTHORIZE_NET) {
			$authorizeNet = new payment_AuthorizeNET($event, $params, $params['amount']);
			$result = $authorizeNet->makePayment();
			
			$result['paymentType'] = model_PaymentType::$AUTHORIZE_NET;
			$result['name'] = $params['firstName'].' '.$params['lastName'];
			$result = array_merge(
				$result, 
				ArrayUtil::keyIntersect($params, array('address', 'city', 'state', 'zip', 'country', 'amount'))
			);
			
			if($result['success']) {
				$params = $result;
				$params['eventId'] = $event['id'];
			}
			else {
				$message = 'There was a problem processing your payment. ';
				$message .= $result['responseText'];
				
				if(isset($errors['general'])) {
					$errors['general'][] = $message;
				}
				else {
					$errors['general'] = array($message);
				}	
				
				return array(
					'success' => FALSE,
					'errors' => $errors
				);
			}
		}
				
		db_reg_PaymentManager::getInstance()->createPayment($group['id'], $params);
		
		return array(
			'success' => TRUE,
			'eventId' => $event['id'],
			'group' => $group
		);
	}
	
	public function removePayment($params) {
		$payment = $this->strictFindById(db_reg_PaymentManager::getInstance(), $params['id']);
		
		db_reg_PaymentManager::getInstance()->deletePayment($params);
		
		return array(
			'eventId' => $params['eventId'],
			'group' => db_reg_GroupManager::getInstance()->find($payment['regGroupId'])
		);
	}
}

?>