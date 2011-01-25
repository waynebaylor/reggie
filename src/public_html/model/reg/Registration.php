<?php

/**
 * This class deals with the registration as the user is filling out the form. It 
 * interacts with the Session not the database.
 */
class model_reg_Registration
{
	public static function getTotalCost($event) {
		$total = 0.0;
		
		foreach(model_reg_Session::getRegistrations() as $index => $reg) {
			$cost = self::getTotalPersonCost($event, $index); 
			$total += $cost;
		}
		
		return $total;
	}
	
	public static function getTotalPersonCost($event, $index) {
		$total = 0.0;
		
		// add up the reg options.
		$eventOptionGroups = model_Event::getRegOptionGroups($event);
		foreach($eventOptionGroups as $group) {
			$total += self::getTotalOptionGroupCost($group, $index);	
		}
		
		// add up the variable quantity options.
		$regTypeId = model_reg_Session::getRegType($index);
		$variableQuantityOptions = model_Event::getVariableQuantityOptions($event);
		foreach($variableQuantityOptions as $option) {
			$name = model_ContentType::$VAR_QUANTITY_OPTION.'_'.$option['id'];
			$quantity = model_reg_Session::getVariableQuantityOption($name, $index);
			$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
			
			$total += $quantity*$price['price'];
		}
		
		return $total;
	}
	
	public static function getConvertedRegistrationsFromSession() {
		$regs = array();
		
		foreach(model_reg_Session::getRegistrations() as $index => $reg) {
			$regs[] = self::convertFromSession($index);
		}
		
		return $regs;
	}
	
	/**
	 * complete means all reg pages have been
	 * filled out, but does not require the payment or summary page.
	 */
	private static function isComplete($event, $index) {
		$count = count(model_reg_Session::getRegistrations());
		
		// put together the list of page IDs that must be completed.
		$pageIds = array();
		$category = model_reg_Session::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);
		foreach($pages as $p) {
			$pageIds[] = $p['id'];
		}
		
		// if they've completed all the reg pages, then we consider it complete.
		$completed = model_reg_Session::getCompletedPages($index);
		$diff = array_diff($pageIds, $completed);
		
		return empty($diff);
	}
	
	/**
	 * convert the session info into the structure given/returned to/from the database.
	 * @param $event
	 * @param $index
	 */
	private static function convertFromSession($index) {
		$category = model_reg_Session::getCategory();
		
		$registration = array(
			'eventId' => model_reg_Session::getEventId(),
			'categoryId' => $category['id'],
			'regTypeId' => model_reg_Session::getRegType($index),
			'information' => array(),
			'regOptionIds' => array(),
			'variableQuantity' => array()
		);

		foreach(model_reg_Session::getContactFields($index) as $key => $value) {
			if(!empty($value)) {
				$registration['information'][] = array(
					'id' => str_replace(model_ContentType::$CONTACT_FIELD.'_', '', $key),
					'value' => $value
				);
			}
		}
	
		// $value can be an id or an array of ids depending on whether
		// it was a radio or checkbox. 
		foreach(model_reg_Session::getRegOptions($index) as $key => $value) {
			if(!empty($key) && !empty($value)) {
				if(is_array($value)) {
					foreach($value as $id) {
						if(is_numeric($id)) {
							$registration['regOptionIds'][] = $id;
						}
					}
				}
				else if(is_numeric($value)) {
					$registration['regOptionIds'][] = $value;	
				}
			}
		}
		
		foreach(model_reg_Session::getVariableQuantityOptions($index) as $key => $value) {
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
		$regTypeId = model_reg_Session::getRegType($index);
		
		foreach($group['options'] as $option) {
			if(self::isRegOptionSelected($option, $index)) {
				$price = model_RegOption::getPrice(array('id' => $regTypeId), $option);
				if(!empty($price)) {
					$total += $price['price'];
				}
				
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
		
		$value = model_reg_Session::getRegOption($name, $index);
		
		// check if the session value matches--it could be a radio or checkbox.
		$optionSelected = ($option['id'] === $value) || (is_array($value) && in_array($option['id'], $value));
		
		return $optionSelected;
	}
	
	public static function removeIncompleteRegistrationsFromSession($event) {
		$incompleteIndexes = array();
		
		foreach(model_reg_Session::getRegistrations() as $index => $reg) {
			if(!self::isComplete($event, $index)) {
				$incomplete[] = $index;
			}
		}
		
		foreach($incompleteIndexes as $index) {
			model_reg_Session::removeRegistration($index);
		}
	}
}

?>