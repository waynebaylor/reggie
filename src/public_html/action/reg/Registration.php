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
		$pageFragment = new fragment_reg_Page($page);
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
			$pageFragment = new fragment_reg_Page($currentPage, $errors);
			
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
			$pageFragment = new fragment_reg_Page($nextPage);
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
		$pageFragment = new fragment_reg_Page($prevPage);
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
		$this->handleRegTypeValues();
		$this->handleContactFieldValues();
		$this->handleRegOptionValues();
		$this->handleVariableQuantityValues();
	}
	
	private function handleRegTypeValues() {
		foreach($_REQUEST as $key => $value) {
			$startsWith = strpos($key, model_ContentType::$REG_TYPE.'_');
			if($startsWith === 0) {
				model_reg_Session::setRegType($value);
			}
		}
	}
	
	private function handleContactFieldValues() {
		foreach($_REQUEST as $key => $value) {
			$startsWith = strpos($key, model_ContentType::$CONTACT_FIELD.'_');
			if($startsWith === 0) {
				model_reg_Session::setContactField($key, $value);				
			}
		}
	}
	
	private function handleRegOptionValues() {
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
		
		// reg option values are from checkboxes/radio buttons, so they
		// may come in as arrays.
		foreach($_REQUEST as $key => $value) {
			$startsWith = strpos($key, model_ContentType::$REG_OPTION.'_');
			if($startsWith === 0) {
				// the reg options are named by group, but the input value is 
				// the reg option id. if the input is a checkbox, then the value 
				// will be an array.
				model_reg_Session::setRegOption($key, $value);
			}
		}
	}
	
	private function handleVariableQuantityValues() {
		foreach($_REQUEST as $key => $value) {
			$startsWith = strpos($key, model_ContentType::$VAR_QUANTITY_OPTION.'_');
			if($startsWith === 0) {
				model_reg_Session::setVariableQuantityOption($key, $value);
			}
		}
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
}

?>