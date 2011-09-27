<?php

class logic_admin_event_EditPaymentOptions extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event
		);
	}
	
	public function savePaymentTypes($params) {
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		$r = array();
		ObjectUtils::populate($r, $params);

		$event['paymentInstructions'] = RequestUtil::getValue('paymentInstructions', '');
		db_EventManager::getInstance()->save($event);
		
		$this->saveCheckDirections($event, $r['paymentTypes'][model_PaymentType::$CHECK]);
		$this->savePoDirections($event, $r['paymentTypes'][model_PaymentType::$PO]);
		$this->saveAuthNetDirections($event, $r['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]);

		return array();
	}
	
	private function saveCheckDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'T') {
			db_payment_CheckDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_CheckDirectionsManager::getInstance()->delete($directions);
		}
	}
	
	private function savePoDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'T') {
			db_payment_PurchaseOrderDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_PurchaseOrderDirectionsManager::getInstance()->delete($directions);
		}
	}
	
	private function saveAuthNetDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'T') {
			db_payment_AuthorizeNetDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_AuthorizeNetDirectionsManager::getInstance()->delete($directions);
		}
	}
}

?>