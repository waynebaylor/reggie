<?php

class RegistrationDispatcher
{
	private $category;
	private $event;
	private $pageId;
	
	function __construct($segments) {
		// URI format is: /event/<code>/<category>[/pageId]
		
		$this->category = $this->getCategory($segments[2]);
		
		$this->event = $this->getEvent($segments[1]);

		// check if the pageId is specified in the request or URL.
		if(isset($_REQUEST['pageId'])) {
			$this->pageId = $_REQUEST['pageId'];	
		}
		else if(count($segments) > 3){
			$this->pageId = $segments[3]; // page id
		}
		else {
			$this->pageId = NULL;
		}		
	}

	/** 
	 * called when the user goes to a URL like /event/...
	 */
	public function getRegistrationAction() { 
		$this->setupSession();
		
		// default action is 'view'.
		if(empty($_REQUEST['action'])) {
			$_REQUEST['action'] = 'view';
		}

		// check if registration is open. this depends on the event dates and 
		// the capacity (if any).
		$eventOpen = strtotime($this->event['regOpen']);
		$eventClosed = strtotime($this->event['regClosed']);
		$now =  time();
		if($now < $eventOpen) {
			// event reg is not open yet.
			return new action_reg_NotOpenYet($this->event);
		}
		else if($now > $eventClosed || $this->eventAtCapacity()) {
			// event reg is closed
			return new action_reg_Closed($this->event);
		}

		// event is open, so find the right page.
		if(model_reg_RegistrationPage::isViewable($this->event, $this->pageId)) {
			switch($this->pageId) {
				case model_reg_RegistrationPage::$PAYMENT_PAGE_ID: 
					// rename the action if we're adding another person.
					if($_REQUEST['action'] === fragment_reg_PaymentPage::$ADD_PERSON_ACTION) {
						$_REQUEST['action'] = 'addPerson';
					}
					
					return new action_reg_Payment($this->event);
					
				case model_reg_RegistrationPage::$SUMMARY_PAGE_ID:
					return new action_reg_Summary($this->event);
					
				case model_reg_RegistrationPage::$CONFIRMATION_PAGE_ID:
					return new action_reg_Confirmation($this->event);
			}
		}

		return new action_reg_Registration($this->event, $this->pageId);
	}

	/**
	 * setup the session object. if the user changes the event or category,
	 * then the session object will be reset.
	 */
	private function setupSession() {
		$resetSession = true;
		if(isset($_SESSION['reg'])) {
			$sessionEventId = model_reg_Session::getEventId();
			$sessionCategory = model_reg_Session::getCategory();
			
			$resetSession = $this->event['id'] !== $sessionEventId ||
							$this->category['id'] !== $sessionCategory['id']; 
		}

		if($resetSession) {
			model_reg_Session::reset($this->category, $this->event);
		}
	}		
	
	/**
	 * match on the first 2 characters of model_Category values.
	 */
	private function getCategory($abbreviation) {
		$categories = model_Category::values(); 
		foreach($categories as $c) { 
			if(strcasecmp($abbreviation, model_Category::code($c)) === 0) {
				return $c;
			}
		}
		
		throw new Exception('Invalid category: "'.$abbreviation.'"');
	}
	
	private function getEvent($code) {
		$event = db_EventManager::getInstance()->findByCode($code);
		
		if(isset($event)) {
			return $event;
		}
		
		throw new Exception('Invalid event: '.$code.'"');
	}
	
	private function eventAtCapacity() {
		if(is_numeric($this->event['capacity']) && $this->event['capacity'] > 0) {
			$regCount = db_reg_RegistrationManager::getInstance()->findEventCount($this->event);
			
			return $regCount >= $this->event['capacity'];
		}
		
		return false;
	}
}

?>