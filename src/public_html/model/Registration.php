<?php

class model_Registration
{
	public static function getTotalCost($event) {
		$total = 0.0;
		
		// add up the reg options.
		$eventOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($eventOptionGroups as $group) {
			$total += self::getTotalGroupCost($group);	
		}
		
		// add up the variable quantity options.
		$regType = model_RegSession::getRegType();
		$variableQuantityOptions = model_Event::getVariableQuantityOptions($event);
		foreach($variableQuantityOptions as $option) {
			$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
			$quantity = model_RegSession::getVariableQuantityOption($name);
			$price = model_RegOption::getPrice($regType, $option);
			
			$total += $quantity*$price['price'];
		}
		
		return number_format($total, 2);
	}
	
	public static function getCompletedFromSession($event) {
		$regs = array();
		
		foreach(model_RegSession::getRegistrations() as $index => $reg) {
			if(self::isComplete($event, $index)) {
				$regs[] = self::convertFromSession($index);
			}
		}
		
		return $regs;
	}
	
	private static function isComplete($event, $index) {
		// put together the list of page IDs that must be completed.
		$pageIds = array();
		$category = model_RegSession::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);
		foreach($pages as $p) {
			$pageIds[] = $p['id'];
		}
		
		if(!empty($event['paymentTypes'])) {
			$pageIds[] = model_RegistrationPage::$PAYMENT_PAGE_ID;	
		}
		
		$pageIds[] = model_RegistrationPage::$SUMMARY_PAGE_ID;
		
		// if they've completed all the reg pages, then we consider it complete.
		$completed = model_RegSession::getCompletedPages($index);
		$diff = array_diff($pageIds, $completed);
		if(empty($diff)) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * convert the session info into the structure given/returned to/from the database.
	 * @param $event
	 * @param $index
	 */
	private static function convertFromSession($index) {
		$category = model_RegSession::getCategory();
		
		$registration = array(
			'categoryId' => $category['id'],
			'eventId' => model_RegSession::getEventId(),
			'regTypeId' => model_RegSession::getRegType($index),
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array(),
			'paymentInfo' => model_RegSession::getPaymentInfo()
		);

		foreach(model_RegSession::getContactFields($index) as $key => $value) {
			if(!empty($value)) {
				$registration['information'][] = array(
					'id' => str_replace(model_ContentType::$CONTACT_FIELD.'_', '', $key),
					'value' => $value
				);
			}
		}
		
		foreach(model_RegSession::getRegOptions($index) as $key => $value) {
			if(!empty($key) && !empty($value) && is_numeric($value)) {
				$registration['regOptionIds'][] = $value;
			}
		}
		
		foreach(model_RegSession::getVariableQuantityOptions($index) as $key => $value) {
			if(!empty($value)) {
				$registration['variableQuantity'][] = array(
					'id' => str_replace(model_ContentType::$VAR_QUANTITY_OPTION.'_', '', $key),
					'quantity' => $value
				);
			}
		}
		
		return $registration;
	}
	
	private static function getTotalGroupCost($group) {
		$total = 0.0;
		$regType = model_RegSession::getRegType();
		
		foreach($group['options'] as $option) {
			if(self::isRegOptionSelected($option)) {
				$price = model_RegOption::getPrice($regType, $option);
				$total += $price['price'];
				
				foreach($option['groups'] as $g) {
					$total += self::getTotalGroupCost($g);
				}
			}	
		}
		
		return $total;
	}
	
	/**
	 * checks if the given option is selected in the current session.
	 * @param $option
	 */
	public static function isRegOptionSelected($option) {
		// put together the session name of the reg option from it's group id.
		$name = model_ContentType::$REG_OPTION.'_'.$option['parentGroupId'];
		
		$value = model_RegSession::getRegOption($name);
		
		// check if the session value matches--it could be a radio or checkbox.
		$optionSelected = ($option['id'] === $value) || (is_array($value) && in_array($option['id'], $value));
		
		return $optionSelected;
	}
}

?>