<?php

class action_admin_event_EditPaymentOptions extends action_BaseAction
{
	function __construct() {
		parent::__construct();
		
		$this->logic = new logic_admin_event_EditPaymentOptions();
		$this->converter = new viewConverter_admin_event_EditPaymentOptions();
	}
	
	public function hasRole($user, $eventId=0, $method='') {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId, $method);
	}
	
	public function view() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0
		));

		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->view($params);
		return $this->converter->getView($info);
	}
	
	public function savePaymentTypes() {
		$params = RequestUtil::getValues(array(
			'eventId' => 0,
			'paymentInstructions' => '',
			'paymentTypes_1_enabled' => 'F',
			'paymentTypes_1_instructions' => '',
			'paymentTypes_2_enabled' => 'F',
			'paymentTypes_2_instructions' => '',
			'paymentTypes_3_enabled' => 'F',
			'paymentTypes_3_instructions' => '',
			'paymentTypes_3_login' => '',
			'paymentTypes_3_transactionKey' => ''
		));
		
		$user = SessionUtil::getUser();
		$this->checkRole($user, $params['eventId']);
		
		$info = $this->logic->savePaymentTypes($params);
		return $this->converter->getSavePaymentTypes($info);
	}
}

?>