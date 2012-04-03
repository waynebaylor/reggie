<?php

class validation_admin_RegOptionPrice
{
	public static function getConfig() {
		return array(
			validation_Validator::required('description', 'Description is required.'),
			validation_Validator::required('startDate', 'Start Date/Time is required.'),
			validation_Validator::required('endDate', 'End Date/Time is required.'),
			validation_Validator::required('price', 'Price is required.'),
			validation_Validator::pattern('price', '/^[0-9]*\.?[0-9]*$/', 'Price can contain numbers and a single decimal.'),
			validation_Validator::required('regTypeIds[]', 'Visible To is required.')
		);
	}
	
	public static function validate($values) {
		$errors = validation_Validator::validate(self::getConfig(), $values);
		
		// 'regOptionId' will be present if this is a new price being added.
		// 'id' will be present if this is an existing price being saved.
		if(isset($values['regOptionId'])) {
			$optionId = $values['regOptionId'];  
		}
		else {
			$price = db_RegOptionPriceManager::getInstance()->find(array(
				'eventId' => $values['eventId'],
				'id' => $values['id']
			));
			
			$optionId = $price['regOptionId'];
		}

		// if the price is associated with a variable quantity option, then:
		//  1) when adding, the value of the action parameter will be 'addVariableQuantityPrice'.
		//  2) when saving, the price can be checked by its manager.
		$isVariableQuantityPrice = isset($price) && db_RegOptionPriceManager::getInstance()->isVariableQuantityPrice($price);
		if($values['action'] === 'addVariableQuantityPrice' || $isVariableQuantityPrice) {
			$option = db_VariableQuantityOptionManager::getInstance()->find($optionId);    
		}
		else {
			$option = db_RegOptionManager::getInstance()->find(array(
				'eventId' => $values['eventId'],
				'id' => $optionId
			));
		}
                		
		// check that the start date doesn't overlap with any existing prices.
		if(empty($errors['startDate']) && empty($errors['endDate'])) {
			if(model_RegOptionPrice::hasOverlap($option, $values)) {
				$errors['startDate'] = 'Date conflicts with existing price.';
				$errors['endDate'] = 'Date conflicts with existing price.';
			}
		}
		
		return $errors;
	}
}

?>