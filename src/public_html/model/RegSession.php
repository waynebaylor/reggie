<?php

class model_RegSession
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
	
	public static function addCompletedPage($pageId, $index = -1) {
		if($index < 0) {
			$index = self::getCurrent();
		}
		
		if(!in_array($pageId, self::getCompletedPages($index))) {
			$_SESSION['reg']['registrations'][$index]['completedPages'][] = $pageId;
		}
	}
	
	/**
	 * Read only.
	 */
	public static function getRegistrations() {
		return $_SESSION['reg']['registrations'];
	}
	
	/**
	 * Read only.
	 * @param $index
	 */
	public static function getRegistration($index) {
		return $_SESSION['reg']['registrations'][$index];
	}
	
	/**
	 * Read only.
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
		
		return isset($fields[$name])? $fields[$name] : '';
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
}

?>