<?php

class db_EventMetadataManager extends db_Manager
{
private static $instance;
	
	protected function __construct() {
		parent::__construct();
	}
	
	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_EventMetadataManager();
		}
		
		return self::$instance;
	}
	
	/**
	 * @param array $params [eventId, contactFieldId, metadata]
	 */
	public function createMetadata($params) {
		$sql = '
			INSERT INTO Event_Metadata (
				eventId,
				contactFieldId,
				metadata
			)
			VALUES (
				:eventId,
				:contactFieldId,
				:metadata
			)
		';
		
		$params = ArrayUtil::keyIntersect($params, array('eventId', 'contactFieldId', 'metadata'));
		
		$this->execute($sql, $params, 'Create metadata.');
	}
	
	public function deleteByEventId($eventId) {
		$sql = '
			DELETE FROM
				Event_Metadata
			WHERE
				eventId = :eventId
		';
		
		$params = array('eventId' => $eventId);
		
		$this->execute($sql, $params, 'Delete by event.');
	}
	
	public function findMetadataByEventId($eventId) {
		$sql = '
			SELECT
				id,
				eventId,
				contactFieldId,
				metadata
			FROM
				Event_Metadata
			WHERE
				eventId = :eventId
		';
		
		$params = array('eventId' => $eventId);
		
		return $this->query($sql, $params, 'Find by event.');
	}
}

?>