<?php

class action_admin_regOption_RegOptionPrice extends action_ValidatorAction
{
	function __construct() {
		parent::__construct();
	}
	
	public function view() {
		$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $_REQUEST['id']);
		
		$eventId = $_REQUEST['eventId'];
		$event = db_EventManager::getInstance()->find($eventId);
		
		return new template_admin_EditRegOptionPrice($event, $price);
	}
	
	public function addVariableQuantityPrice() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$option = $this->strictFindById(db_VariableQuantityOptionManager::getInstance(), $_REQUEST['regOptionId']);
		
		$price = RequestUtil::getParameters(array(
			'regOptionId',
			'description',
			'startDate',
			'endDate',
			'price',
			'regTypeIds'
		));
		
		db_RegOptionPriceManager::getInstance()->createVariableQuantityPrice($price);
		
		$option = db_VariableQuantityOptionManager::getInstance()->find($option['id']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionPrice_List($event, $option);
	}
	
	public function addRegOptionPrice() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$option = $this->strictFindById(db_RegOptionManager::getInstance(), $_REQUEST['regOptionId']);
		
		$price = RequestUtil::getParameters(array(
			'regOptionId',
			'description',
			'startDate',
			'endDate',
			'price',
			'regTypeIds'
		));
		
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice($price);
		
		$option = db_RegOptionManager::getInstance()->find($option['id']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionPrice_List($event, $option);
	}
	
	public function removePrice() {
		$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $_REQUEST['id']);
		
		db_RegOptionPriceManager::getInstance()->delete($price);
		
		$option = db_RegOptionManager::getInstance()->find($price['regOptionId']);
		$event = db_EventManager::getInstance()->find($_REQUEST['eventId']);
		
		return new fragment_regOptionPrice_List($event, $option);
	}
	
	public function savePrice() {
		$errors = $this->validate();
		
		if(!empty($errors)) {
			return new fragment_validation_ValidationErrors($errors);
		}
		
		$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $_REQUEST['id']);
		
		$price = array();
		ObjectUtils::populate($price, $_REQUEST);
		
		db_RegOptionPriceManager::getInstance()->save($price);
		
		return new fragment_Success();
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		// 'regOptionId' will be present if this is a new price being added.
		// 'id' will be present if this is an existing price being saved.
		if(isset($_REQUEST['regOptionId'])) {
			$optionId = $_REQUEST['regOptionId'];  
		}
		else {
			$price = $this->strictFindById(db_RegOptionPriceManager::getInstance(), $_REQUEST['id']);
			$optionId = $price['regOptionId'];
		}

		// if the price is associated with a variable quantity option, then:
		//  1) when adding, the value of the action parameter will be 'addVariableQuantityPrice'.
		//  2) when saving, the price can be checked by its manager.
		$isVariableQuantityPrice = isset($price) && db_RegOptionPriceManager::getInstance()->isVariableQuantityPrice($price);
		if($_REQUEST['action'] === 'addVariableQuantityPrice' || $isVariableQuantityPrice) {
			$option = db_VariableQuantityOptionManager::getInstance()->find($optionId);	
		}
		else {
			$option = db_RegOptionManager::getInstance()->find($optionId);
		}
			
		$price = RequestUtil::getParameters(array(
			'id', // may not be set if coming from add action
			'startDate',
			'endDate',
			'regTypeIds'
		));
					
		// check that the start date doesn't overlap with any existing prices.
		if(empty($errors['startDate']) && empty($errors['endDate'])) {
			if(model_RegOptionPrice::hasOverlap($option, $price)) {
				$errors['startDate'] = 'Date conflicts with existing price.';
				$errors['endDate'] = 'Date conflicts with existing price.';
			}
		}

		return $errors;
	}
	
	protected function getValidationConfig() {
		return array(
			array(
				'name' => 'description',
				'value' => $_REQUEST['description'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Description is required.'
					)
				)
			),
			array(
				'name' => 'startDate',
				'value' => $_REQUEST['startDate'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Start Date/Time is required.'
					)
				)
			),
			array(
				'name' => 'endDate',
				'value' => $_REQUEST['endDate'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'End Date/Time is required.'
					)
				)
			),
			array(
				'name' => 'price',
				'value' => $_REQUEST['price'],
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Price is required.'
					),
					array(
						'name' => 'pattern',
						'regex' => '/^[0-9]*\.?[0-9]*$/',
						'text' => 'Price can contain numbers and a single decimal.'
					)
				)
			),
			array(
				'name' => 'regTypeIds[]',
				'value' => RequestUtil::getValueAsArray('regTypeIds', array()),
				'restrictions' => array(
					array(
						'name' => 'required',
						'text' => 'Visible To is required.'
					)
				)
			)
		);
	}
}

?>