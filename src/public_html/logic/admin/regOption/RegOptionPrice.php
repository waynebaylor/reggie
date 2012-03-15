<?php

class logic_admin_regOption_RegOptionPrice extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $params['id']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'actionMenuEventLabel' => $event['code'],
			'eventId' => $params['eventId'],
			'event' => $event,
			'price' => $price,
			'breadcrumbsParams' => db_BreadcrumbManager::getInstance()->findRegOptionPriceCrumbs($params['id'])
		);
	}
	
	public function addRegOptionPrice($params) {
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice($params);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function addVariableQuantityPrice($params) {
		db_RegOptionPriceManager::getInstance()->createVariableQuantityPrice($params);
		
		$option = db_VariableQuantityOptionManager::getInstance()->find($params['regOptionId']);
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function removePrice($params) {
		$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $params['id']);
		
		db_RegOptionPriceManager::getInstance()->delete($price);
		
		$option = db_RegOptionManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $price['regOptionId']
		));
		
		$event = db_EventManager::getInstance()->find($params['eventId']);
		
		return array(
			'eventId' => $params['eventId'],
			'event' => $event,
			'option' => $option
		);
	}
	
	public function savePrice($params) {
		db_RegOptionPriceManager::getInstance()->save($params);
		
		return $params;
	}
}

?>