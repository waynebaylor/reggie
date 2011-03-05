<?php

class db_EventTemplate extends db_Manager
{
	private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_EventTemplate();
		}
		
		return self::$instance;
	}
	
	public function createDefaults($eventId) {
		// create built-in reports.
		db_ReportManager::getInstance()->createPaymentsToDate($eventId);
		db_ReportManager::getInstance()->createAllRegToDate($eventId);
		db_ReportManager::getInstance()->createOptionCount($eventId);
		db_ReportManager::getInstance()->createRegTypeBreakdown($eventId);
		
		// create event pages.
		$visibleToCategoryIds = array(1); // attendee only.
		$this->createRegTypeTemplatePage($eventId, $visibleToCategoryIds);
		$this->createContactInfoTemplatePage($eventId, $visibleToCategoryIds);
		$this->createConferenceRegTemplatePage($eventId, $visibleToCategoryIds); 
		$this->createSpecialEventsTemplatePage($eventId, $visibleToCategoryIds);
		$this->createSurveyTemplatePage($eventId, $visibleToCategoryIds);
		
	}
	
	private function createRegTypeTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Registration Type', $categoryIds);
		
		$textSectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'reg type text', model_ContentType::$TEXT);
		db_PageSectionManager::getInstance()->save(array(
			'id' => $textSectionId,
			'name' => 'reg type text',
			'text' => 'Plese choose a registration type below.',
			'numbered' => 'F'
		));
		
		$regTypeSectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'reg types', model_ContentType::$REG_TYPE);
		
		db_RegTypeManager::getInstance()->createRegType($eventId, $regTypeSectionId, 'Member', 'M', $categoryIds);
		
		db_RegTypeManager::getInstance()->createRegType($eventId, $regTypeSectionId, 'Non-Member', 'NM', $categoryIds);
	}

	private function createContactInfoTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Contact Information', $categoryIds);
		
		$contactInfoSectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'contact info', model_ContentType::$CONTACT_FIELD);
		
		db_ContactFieldManager::getInstance()->createContactField(array(
			'eventId' => $eventId,
			'sectionId' => $contactInfoSectionId,
			'code' => 'FN',
			'displayName' => 'First Name',
			'formInputId' => model_FormInput::$TEXT,
			'attributes' => array(),
			'validationRules' => array(
				model_Validation::$REQUIRED => 'T'
			),
			'regTypeIds' => array(-1)
		));
		
		db_ContactFieldManager::getInstance()->createContactField(array(
			'eventId' => $eventId,
			'sectionId' => $contactInfoSectionId,
			'code' => 'LN',
			'displayName' => 'Last Name',
			'formInputId' => model_FormInput::$TEXT,
			'attributes' => array(),
			'validationRules' => array(
				model_Validation::$REQUIRED => 'T'
			),
			'regTypeIds' => array(-1)
		));
		
		db_ContactFieldManager::getInstance()->createContactField(array(
			'eventId' => $eventId,
			'sectionId' => $contactInfoSectionId,
			'code' => 'email',
			'displayName' => 'Email',
			'formInputId' => model_FormInput::$TEXT,
			'attributes' => array(
				model_Attribute::$SIZE => 30
			),
			'validationRules' => array(
				model_Validation::$REQUIRED => 'T'
			),
			'regTypeIds' => array(-1)
		));
	}

	private function createConferenceRegTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Conference Registration', $categoryIds);
		
		$sectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'reg options', model_ContentType::$REG_OPTION);
		
		// option group.
		$optGroupId = db_GroupManager::getInstance()->createGroupUnderSection(array(
			'eventId' => $eventId,
			'sectionId' => $sectionId,
			'required' => 'T',
			'multiple' => 'F',
			'minimum' => 0,
			'maximum' => 0
		));
		
		// first option.
		$optOneId = db_RegOptionManager::getInstance()->createRegOption(array(
			'eventId' => $eventId,
			'parentGroupId' => $optGroupId,
			'code' => 'OPT1',
			'description' => 'This is option number one',
			'capacity' => 0,
			'defaultSelected' => 'T',
			'showPrice' => 'T'
		));
		
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice(array(
			'eventId' => $eventId,
			'regOptionId' => $optOneId,
			'description' => 'full price',
			'startDate' => date(db_Manager::$DATE_FORMAT),
			'endDate' => date(db_Manager::$DATE_FORMAT, time()+604800),
			'price' => '100.00',
			'regTypeIds' => array(-1)
		));
		
		// second option.
		$optTwoId = db_RegOptionManager::getInstance()->createRegOption(array(
			'eventId' => $eventId,
			'parentGroupId' => $optGroupId,
			'code' => 'OPT2',
			'description' => 'This is option number two',
			'capacity' => 0,
			'defaultSelected' => 'F',
			'showPrice' => 'T'
		));
		
		db_RegOptionPriceManager::getInstance()->createRegOptionPrice(array(
			'eventId' => $eventId,
			'regOptionId' => $optTwoId,
			'description' => 'full price',
			'startDate' => date(db_Manager::$DATE_FORMAT),
			'endDate' => date(db_Manager::$DATE_FORMAT, time()+604800),
			'price' => '1.99',
			'regTypeIds' => array(-1)
		));
	}

	private function createSpecialEventsTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Special Events', $categoryIds);
		
		$sectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'special', model_ContentType::$VAR_QUANTITY_OPTION);
		
		$varOptId = db_VariableQuantityOptionManager::getInstance()->createOption(array(
			'eventId' => $eventId,
			'sectionId' => $sectionId,
			'code' => 'SPE1',
			'description' => 'Tickets for Special Event',
			'capacity' => 0,
		));
		
		db_RegOptionPriceManager::getInstance()->createVariableQuantityPrice(array(
			'eventId' => $eventId,
			'regOptionId' => $varOptId,
			'description' => 'full price',
			'startDate' => date(db_Manager::$DATE_FORMAT),
			'endDate' => date(db_Manager::$DATE_FORMAT, time()+604800),
			'price' => '25.00',
			'regTypeIds' => array(-1)
		));
	}

	private function createSurveyTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Survey', $categoryIds);

		$sectionId = db_PageSectionManager::getInstance()->createSection($eventId, $pageId, 'survey questions', model_ContentType::$CONTACT_FIELD);
		db_PageSectionManager::getInstance()->save(array(
			'id' => $sectionId,
			'name' => 'survey questions',
			'text' => '',
			'numbered' => 'T'
		));
		
		db_ContactFieldManager::getInstance()->createContactField(array(
			'eventId' => $eventId,
			'sectionId' => $sectionId,
			'code' => 'Q1',
			'displayName' => 'Please enter your answer below.',
			'formInputId' => model_FormInput::$TEXT,
			'attributes' => array(),
			'validationRules' => array(),
			'regTypeIds' => array(-1)
		));
	}
}

?>