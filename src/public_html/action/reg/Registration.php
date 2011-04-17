<?php

class action_reg_Registration extends action_ValidatorAction
{
	private $event;
	private $pageId;
	
	function __construct($event, $pageId) {
		parent::__construct();	
		
		$this->event = $event;
		$this->pageId = $pageId;
		
		// default to the event's first page.
		if(!model_reg_RegistrationPage::isViewable($this->event, $this->pageId)) {
			$firstPage = model_reg_RegistrationPage::getFirstPage($this->event);
			$this->pageId = $firstPage['id'];
		}
	}
	
	public function view() {
		$page = model_Event::getPageById($this->event, $this->pageId);
		$pageFragment = new fragment_reg_Page($this->event, $page);
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => $page['title'],
			'id' => $page['id'],
			'page' => $pageFragment
		));
	}
	
	public function Next() {
		// need to save the form values to the session first, so if there
		// are validation errors the fields will be repopulated
		$this->handleFormValues();
		
		$errors = $this->validate();
		
		// if there are validation errors, then re-display the page
		// with the necessary error messages.
		if(!empty($errors)) {
			$currentPage = model_Event::getPageById($this->event, $this->pageId);
			$pageFragment = new fragment_reg_Page($this->event, $currentPage);
			
			return new template_reg_BasePage(array(
				'event' => $this->event,
				'title' => $currentPage['title'],
				'id' => $currentPage['id'],
				'page' => $pageFragment,
				'errors' => $errors
			));				
		}

		// no validation errors, so go to the next page.
		
		model_reg_Session::addCompletedPage($this->pageId);
		
		if(model_reg_RegistrationPage::isLastRegistrationPage($this->event, $this->pageId)) {
			// if the event doesn't have any payment types enabled,
			// then skip payment info page and go to summary payge. 
			if(!empty($this->event['paymentTypes'])) {
				$paymentInfo = new action_reg_Payment($this->event);
				return $paymentInfo->view();
			}
			else {
				$summaryAction = new action_reg_Summary($this->event);
				return $summaryAction->view();
			}
		}
		else {
			$nextPage = model_reg_RegistrationPage::getNextPage($this->event, $this->pageId);
			$pageFragment = new fragment_reg_Page($this->event, $nextPage);
			return new template_reg_BasePage(array(
				'event' => $this->event,
				'title' => $nextPage['title'],
				'id' => $nextPage['id'],
				'page' => $pageFragment
			));
		}
	}

	public function Previous() {
		$prevPage = model_reg_RegistrationPage::getPrevPage($this->event, $this->pageId);
		$pageFragment = new fragment_reg_Page($this->event, $prevPage);
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => $prevPage['title'],
			'id' => $prevPage['id'],
			'page' => $pageFragment
		));
	}
	
	public function validate($fieldNames = array()) {
		$errors = parent::validate($fieldNames);
		
		$page = model_Event::getPageById($this->event, $this->pageId);
		$pageValidator = new validation_reg_PageValidator($this->event, $page);
		
		$errors = array_merge($errors, $pageValidator->validate());
		
		return $errors;
	}
	
	private function handleFormValues() {
		// get all reg option groups displayed on page and clear their values
		// before saving the current values. this is needed because not checking
		// a previously selected option may mean it is not included in the submitted
		// form values.
		$page = model_Event::getPageById($this->event, $this->pageId);
		foreach($page['sections'] as $section) {
			if(model_Section::containsRegOptions($section)) {
				foreach($section['content'] as $group) {
					$this->clearRegOptionGroupSessionValue($group);
				}
			}
		}

		foreach($_REQUEST as $key => $value) {
			if(strpos($key, model_ContentType::$REG_TYPE.'_') === 0) {
				$this->setRegTypeValue($value);
			}
			else if(strpos($key, model_ContentType::$CONTACT_FIELD.'_') === 0) {
				$this->setContactFieldValue($key, $value);
			}
			else if(strpos($key, model_ContentType::$REG_OPTION.'_') === 0) {
				$this->setRegOptionValue($key, $value);
			}
			else if(strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_') === 0) {
				$this->setVariableQuantityValue($key, $value);
			}
		}
	}
	
	private function setRegTypeValue($regTypeValue) {
		// if user changes reg type, then remove any fields, options, etc
		// not applicable to the new reg type.
		$currentRegType = model_reg_Session::getRegType();

		if($currentRegType !== $regTypeValue) { 
			$this->filterOutInvalidContactFields($regTypeValue);
			
			// clear out any selected reg options since they may not be valid with
			// the new reg type.
			foreach(model_reg_Session::getRegOptions() as $key => $value) {
				model_reg_Session::setRegOption($key, null);
			}
			
			// clear out any variable quantity options since they may not be valid
			// with the new reg type.
			foreach(model_reg_Session::getVariableQuantityOptions() as $key => $value) {
				model_reg_Session::setVariableQuantityOption($key, null);
			}
			
			model_reg_Session::resetCompletedPages($this->pageId);
		}

		model_reg_Session::setRegType($regTypeValue);
	}
	
	private function setContactFieldValue($key, $value) {
		model_reg_Session::setContactField($key, $value);
	}
	
	private function setRegOptionValue($key, $value) {
		// reg option values are from checkboxes/radio buttons, so they
		// may come in as arrays.
		//
		// the reg options are named by group, but the input value is
		// the reg option id. 
		//
		// if the input is a checkbox, then the value will be an array.
		model_reg_Session::setRegOption($key, $value);
	}
	
	private function setVariableQuantityValue($key, $value) {
		model_reg_Session::setVariableQuantityOption($key, $value);
	}
	
	private function clearRegOptionGroupSessionValue($group) {
		$name = model_ContentType::$REG_OPTION.'_'.$group['id'];
		
		model_reg_Session::setRegOption($name, NULL);
		
		// recursively clear any values nested in the given group.
		foreach($group['options'] as $option) {
			foreach($option['groups'] as $g) {
				$this->clearRegOptionGroupSessionValue($g);
			}
		}
	}
	
	private function filterOutInvalidContactFields($regTypeId) {
		$regType = model_Event::getRegTypeById($this->event, $regTypeId);
		
		// get a list of all valid field ids.
		$eventFields = model_Event::getInformationFields($this->event);
		$validFields = array();
		foreach($eventFields as $field) {
			$validFields[$field['id']] = $field;
		}
		
		// remove invalid field ids from the session.
		$currentFields = model_reg_Session::getContactFields();
		foreach($currentFields as $key => $f) {
			$id = str_replace(model_ContentType::$CONTACT_FIELD.'_', '', $key); 
			if(!array_key_exists($id, $validFields) || !model_ContactField::isVisibleTo($validFields[$id], $regType)) {
				model_reg_Session::setContactField($key, null); 
			}
		}
	}
}

?>