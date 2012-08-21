<?php

class logic_admin_event_EditMetadata extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($params) {
		return array(
			'eventId' => $params['eventId'],
			'metadataToField' => $this->getMetadataToField($params['eventId'])
		);
	}
	
	public function saveMetadata($params) {
		db_EventMetadataManager::getInstance()->deleteByEventId($params['eventId']);
		
		if($params['firstName'] !== 0) {
			db_EventMetadataManager::getInstance()->createMetadata(array(
				'eventId' => $params['eventId'],
				'contactFieldId' => $params['firstName'],
				'metadata' => 'FIRST_NAME'
			));
		}
		
		if($params['lastName'] !== 0) {
			db_EventMetadataManager::getInstance()->createMetadata(array(
				'eventId' => $params['eventId'],
				'contactFieldId' => $params['lastName'],
				'metadata' => 'LAST_NAME'
			));
		}
		
		if($params['email'] !== 0) {
			db_EventMetadataManager::getInstance()->createMetadata(array(
				'eventId' => $params['eventId'],
				'contactFieldId' => $params['email'],
				'metadata' => 'EMAIL'
			));
		}
		
		return array('eventId' => $params['eventId']);
	}
	
	private function getMetadataToField($eventId) {
		// defaults.
		$metadataToField = array(
			'FIRST_NAME' => 0,
			'LAST_NAME' => 0,
			'EMAIL' => 0
		);
		
		$metadata = db_EventMetadataManager::getInstance()->findMetadataByEventId($eventId);
		foreach($metadata as $m) {
			$metadataToField[$m['metadata']] = $m['contactFieldId'];	
		}
		
		return $metadataToField;
	}
}

?>