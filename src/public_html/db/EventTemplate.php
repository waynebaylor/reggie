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
				array(model_Validation::$REQUIRED, 'T'),
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
				array(model_Validation::$REQUIRED, 'T'),
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
				array(model_Attribute::$SIZE, 30)
			),
			'validationRules' => array(
				array(model_Validation::$REQUIRED, 'T'),
			),
			'regTypeIds' => array(-1)
		));
	}

	private function createConferenceRegTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Conference Registration', $categoryIds);
	}

	private function createSpecialEventsTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Special Events', $categoryIds);

	}

	private function createSurveyTemplatePage($eventId, $categoryIds) {
		$pageId = db_PageManager::getInstance()->createPage($eventId, 'Survey', $categoryIds);

	}
}

?>