<?php

class model_Event
{
	public static function getInformationFields($event) {
		$fields = array();
		
		foreach($event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(model_Section::containsContactFields($section)) {
					$fields = array_merge($fields, $section['content']);
				}
			}	
		}
		
		return $fields;
	}
	
	/**
	 * returns the top level reg option groups for the given event.
	 * @param $event
	 */
	public static function getRegOptionGroups($event) {
		$options = array();
		
		foreach($event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(model_Section::containsRegOptions($section)) {
					$options = array_merge($options, $section['content']);
				}
			}	
		}
		
		return $options;
	}
	
	/**
	 * returns the page with the given id in the given event. 
	 * @param $event
	 * @param $pageId
	 */
	public static function getPageById($event, $pageId) {
		foreach($event['pages'] as $page) {
			if(intval($page['id'], 10) === intval($pageId, 10)) {
				return $page;
			}
		}
		
		return NULL;
	}
	
	/**
	 * returns the reg type with the given id in the given event.
	 * @param $event
	 * @param $regTypeId
	 */
	public static function getRegTypeById($event, $regTypeId) {
		foreach($event['regTypes'] as $regType) {
			if(intval($regType['id'], 10) === intval($regTypeId, 10)) {
				return $regType;
			}
		}
		
		return NULL;
	}
	
	/**
	 * returns whether or not the given payment type is enabled in the
	 * given event.
	 */
	public static function isPaymentTypeEnabled($event, $type) {
		$details = self::getPaymentTypeDirections($event, $type);
		
		return !empty($details);
	}
	
	public static function getPaymentTypeDirections($event, $type) {
		$eventPaymentTypes = $event['paymentTypes'];

		$directions = empty($eventPaymentTypes[$type['id']])? NULL : $eventPaymentTypes[$type['id']];
		
		return $directions;
	}
	
	public static function getVariableQuantityOptions($event) {
		$options = array();
		
		foreach($event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(model_Section::containsVariableQuantityOptions($section)) {
					$options = array_merge($options, $section['content']);
				}
			}
		}
		
		return $options;
	}
	
	public static function getSectionById($event, $sectionId) {
		foreach($event['pages'] as $page) {
			foreach($page['sections'] as $section) {
				if(intval($section['id'], 10) === intval($sectionId, 10)) {
					return $section;
				}				
			}
		}
		
		return NULL;
	}
}

?>