<?php

class model_Registration
{
	public static function getTotalCost($event) {
		$total = 0.0;
		
		foreach(model_RegSession::getRegistrations() as $index => $reg) {
			$total += self::getTotalPersonCost($event, $index);
		}
		
		return number_format($total, 2);
	}
	
	private static function getTotalPersonCost($event, $index) {
		$total = 0.0;
		
		// add up the reg options.
		$eventOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($eventOptionGroups as $group) {
			$total += self::getTotalOptionGroupCost($group, $index);	
		}
		
		// add up the variable quantity options.
		$regType = model_RegSession::getRegType($index);
		$variableQuantityOptions = model_Event::getVariableQuantityOptions($event);
		foreach($variableQuantityOptions as $option) {
			$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
			$quantity = model_RegSession::getVariableQuantityOption($name, $index);
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
	
	private static function getTotalOptionGroupCost($group, $index) {
		$total = 0.0;
		$regType = model_RegSession::getRegType($index);
		
		foreach($group['options'] as $option) {
			if(self::isRegOptionSelected($option, $index)) {
				$price = model_RegOption::getPrice($regType, $option);
				$total += $price['price'];
				
				foreach($option['groups'] as $g) {
					$total += self::getTotalOptionGroupCost($g, $index);
				}
			}	
		}
		
		return $total;
	}
	
	/**
	 * checks if the given option is selected in the current session.
	 */
	public static function isRegOptionSelected($option, $index) {
		// put together the session name of the reg option from it's group id.
		$name = model_ContentType::$REG_OPTION.'_'.$option['parentGroupId'];
		
		$value = model_RegSession::getRegOption($name, $index);
		
		// check if the session value matches--it could be a radio or checkbox.
		$optionSelected = ($option['id'] === $value) || (is_array($value) && in_array($option['id'], $value));
		
		return $optionSelected;
	}
}

?>