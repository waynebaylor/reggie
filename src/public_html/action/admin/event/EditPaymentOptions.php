<?php

class action_admin_event_EditPaymentOptions extends action_BaseAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$event = $this->strictFindById(db_EventManager::getInstance(), $_REQUEST['id']);
		
		return new template_admin_EditPaymentOptions($event);
	}
	
	public function savePaymentTypes() {
		$event = $this->strictFindById(db_EventManager::getInstance(), $_REQUEST['eventId']);
		
		$r = array();
		ObjectUtils::populate($r, $_REQUEST);

		$this->saveCheckDirections($event, $r['paymentTypes'][model_PaymentType::$CHECK]);
		$this->savePoDirections($event, $r['paymentTypes'][model_PaymentType::$PO]);
		$this->saveAuthNetDirections($event, $r['paymentTypes'][model_PaymentType::$AUTHORIZE_NET]);

		return new fragment_Success();
	}
	
	private function saveCheckDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'true') {
			db_payment_CheckDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_CheckDirectionsManager::getInstance()->delete($directions);
		}
	}
	
	private function savePoDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'true') {
			db_payment_PurchaseOrderDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_PurchaseOrderDirectionsManager::getInstance()->delete($directions);
		}
	}
	
	private function saveAuthNetDirections($event, $directions) {
		$directions['eventId'] = $event['id'];
		
		if($directions['enabled'] === 'true') {
			db_payment_AuthorizeNetDirectionsManager::getInstance()->create($directions);
		}
		else {
			db_payment_AuthorizeNetDirectionsManager::getInstance()->delete($directions);
		}
	}
}

?>