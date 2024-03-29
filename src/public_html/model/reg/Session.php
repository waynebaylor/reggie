<?php

class model_reg_Session
{
public static function reset($category, $event) {
		$_SESSION['reg'] = array(
			'category' => $category,
			'event' => $event['id'],
			'current' => 0,
			'registrations' => array(
				array(
					'completedPages' => array(),
					'regType' => NULL,
					'information' => array(),
					'regOptions' => array(),
					'variableQuantity' => array()
				)
			),
			'payment' => array(
				'paymentType' => NULL
			)
		);
	}
	
	public static function addPerson($event) {
		$groupReg = $event['groupRegistration'];
		
		if($groupReg['enabled'] === 'T') {
			$newReg = array(
				'completedPages' => array(),
				'regType' => NULL,
				'information' => array(),
				'regOptions' => array(),
				'variableQuantity' => array()
			);
				
			if($groupReg['defaultRegType'] === 'T') {
				$newReg['regType'] = self::getRegType(self::getCurrent());
			}
			
			foreach($groupReg['fields'] as $field) {
				$name = model_ContentType::$CONTACT_FIELD.'_'.$field['contactFieldId'];
				$value = self::getContactField($name, self::getCurrent());
				
				$newReg['information'][$name] = $value; 
			}

			$_SESSION['reg']['registrations'][] = $newReg;
				
			self::setCurrent(count($_SESSION['reg']['registrations'])-1);
		}
	}
	
	/**
	 * Read only.
	 */
	public static function getCategory() {
		return $_SESSION['reg']['category'];	
	}
	
	public static function setCategory($c) {
		$_SESSION['reg']['category'] = $c;
	}
	
	/**
	 * Read only.
	 */
	public static function getEventId() {
		return $_SESSION['reg']['event'];	
	}
	
	public static function setEventId($eventId) {
		$_SESSION['reg']['event'] = $eventId;	
	}
	
	/**
	 * Read only.
	 */
	public static function getCurrent() {
		return $_SESSION['reg']['current'];		
	}
	
	public static function setCurrent($value) {
		$_SESSION['reg']['current'] = $value;
	}
	
	/**
	 * Read only.
	 */
	public static function getCompletedPages($index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		return $_SESSION['reg']['registrations'][$index]['completedPages'];	
	}
	
	public static function addCompletedPage($pageId) {
		$index = self::getCurrent();
		
		// if one registrant finishes the payment page, then we consider all
		// registrants in the group to have finished it. this is important: if 
		// the current registrant is removed from the summary page, then in 
		// order to submit the updated summary we need the remaining registrant(s)
		// to be treated as if they've completed the payment page too.
		if($pageId == model_reg_RegistrationPage::$PAYMENT_PAGE_ID) {
			foreach($_SESSION['reg']['registrations'] as $i => $r) {
				if(!in_array($pageId, self::getCompletedPages($i))) {
					$_SESSION['reg']['registrations'][$i]['completedPages'][] = $pageId;
				}
			}	
		}
		// otherwise just add the completed page to the current registrant.
		else {
			if(!in_array($pageId, self::getCompletedPages($index))) {
				$_SESSION['reg']['registrations'][$index]['completedPages'][] = $pageId;
			}
		}
	}
	
	/**
	 * removes any pages after the given page id, thus requiring the user
	 * to click through them.
	 */
	public static function resetCompletedPages($pageId) {
		$completed = array();
		foreach(self::getCompletedPages() as $p) {
			$completed[] = $p;
	
			// don't add any pages after the given id.
			if($p === $pageId) {
				break;
			}
		}
		
		$index = self::getCurrent();
		$_SESSION['reg']['registrations'][$index]['completedPages'] = $completed;
	}
	
	/**
	 * Read only.
	 */
	public static function getRegistrations() {
		return $_SESSION['reg']['registrations'];
	}
	
	/**
	 * Read only. The reg type id.
	 */
	public static function getRegType($index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		return $_SESSION['reg']['registrations'][$index]['regType'];
	}
	
	public static function setRegType($r, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		$_SESSION['reg']['registrations'][$index]['regType'] = $r; 
	}

	/**
	 * Read only.
	 * @param $name
	 */
	public static function getContactField($name, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
			
		$fields = self::getContactFields($index);
		
		$value = isset($fields[$name])? $fields[$name] : '';
	
		return $value;
	}
	
	public static function setContactField($name, $value, $index = -1) { 
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		// get rid of the '[]', since php converts the value to an array automatically.
		if(substr($name, -2) === '[]') {
			$name = substr($name, 0, -2);
		}
		
		$_SESSION['reg']['registrations'][$index]['information'][$name] = $value;
	}
	
	/**
	 * Read only.
	 */
	public static function getContactFields($index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		return $_SESSION['reg']['registrations'][$index]['information'];
	}
	
	/**
	 * Read only.
	 */
	public static function getRegOptions($index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		return $_SESSION['reg']['registrations'][$index]['regOptions'];
	}
	
	/**
	 * Read only.
	 * @param $name
	 */
	public static function getRegOption($name, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		$opts = self::getRegOptions($index);
		return isset($opts[$name])? $opts[$name] : '';
	}
	
	public static function setRegOption($name, $value, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		$_SESSION['reg']['registrations'][$index]['regOptions'][$name] = $value;
	}
	
	/**
	 * Read only.
	 */
	public static function getVariableQuantityOptions($index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		return $_SESSION['reg']['registrations'][$index]['variableQuantity'];
	}
	
	public static function getVariableQuantityOption($name, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		$opts = self::getVariableQuantityOptions($index);
		return isset($opts[$name])? $opts[$name] : '';
	}
	
	public static function setVariableQuantityOption($name, $value, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		$_SESSION['reg']['registrations'][$index]['variableQuantity'][$name] = $value;
	}
	
	public static function getPaymentInfo() {
		return $_SESSION['reg']['payment'];
	}
	
	public static function setPaymentInfo($info) {
		$_SESSION['reg']['payment'] = $info;
	}
	
	public static function removeRegistration($index) {
		unset($_SESSION['reg']['registrations'][$index]);
		// re-key the array.
		$_SESSION['reg']['registrations'] = array_values($_SESSION['reg']['registrations']);
	}
}

?>