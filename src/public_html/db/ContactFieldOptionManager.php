<?php

class db_ContactFieldOptionManager extends db_OrderableManager
{
	private static $instance;

	protected function __construct() {
		parent::__construct();
	}

	protected function getTableName() {
		return 'ContactFieldOption';
	}

	public static function getInstance() {
		if(empty(self::$instance)) {
			self::$instance = new db_ContactFieldOptionManager();
		}

		return self::$instance;
	}

	/**
	 *
	 * @param array $params [eventId, id]
	 */
	public function find($params) {
		$sql = '
			SELECT
				ContactFieldOption.id,
				ContactFieldOption.contactFieldId,
				ContactFieldOption.displayName,
				ContactFieldOption.defaultSelected,
				ContactFieldOption.displayOrder
			FROM
				ContactFieldOption
			WHERE
				ContactFieldOption.id = :id
			AND
				ContactFieldOption.contactFieldId
			IN (
				SELECT ContactField.id 
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
			ORDER BY
				ContactFieldOption.displayOrder
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));

		return $this->queryUnique($sql, $params, 'Find contact field option.');
	}

	/**
	 *
	 * @param array $params [eventId, contactFieldId]
	 */
	public function findByField($params) {
		$sql = '
			SELECT
				ContactFieldOption.id,
				ContactFieldOption.contactFieldId,
				ContactFieldOption.displayName,
				ContactFieldOption.defaultSelected,
				ContactFieldOption.displayOrder
			FROM
				ContactFieldOption
			WHERE
				ContactFieldOption.contactFieldId = :contactFieldId
			AND
				ContactFieldOption.contactFieldId
			IN (
				SELECT ContactField.id 
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)	
			ORDER BY
				ContactFieldOption.displayOrder
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'contactFieldId'));

		return $this->query($sql, $params, 'Find contact field options.');
	}

	/**
	 *
	 * @param array $params [eventId, contactFieldId, displayName, defaultSelected]
	 */
	public function createOption($params) {
		$field = db_ContactFieldManager::getInstance()->find(array(
			'eventId' => $params['eventId'],
			'id' => $params['contactFieldId']
		));

		$sql = '
			INSERT INTO
				ContactFieldOption(
					contactFieldId,
					displayName,
					defaultSelected,
					displayOrder
				)
			VALUES(
				:contactFieldId,
				:displayName,
				:defaultSelected,
				:displayOrder
			)
		';

		$params = ArrayUtil::keyIntersect($params, array(
			'displayName',
			'defaultSelected'		
			));
			$params['contactFieldId'] = $field['id'];
			$params['displayOrder'] = $this->getNextOrder();

			$this->execute($sql, $params, 'Create contact field option.');
	}

	/**
	 *
	 * @param array $params [eventId, id]
	 */
	public function delete($params) {
		$sql = '
			DELETE FROM
				ContactFieldOption
			WHERE
				ContactFieldOption.id = :id
			AND
				ContactFieldOption.contactFieldId
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'id'));

		$this->execute($sql, $params, 'Delete contact field option.');
	}

	/**
	 *
	 * @param array $params [eventId, contactFieldId]
	 */
	public function removeOptions($params) {
		$sql = '
			DELETE FROM
				ContactFieldOption
			WHERE
				ContactFieldOption.contactFieldId = :contactFieldId
			AND
				ContactFieldOption.contactFieldId
			IN (
				SELECT ContactField.id
				FROM ContactField
				WHERE ContactField.eventId = :eventId
			)
		';

		$params = ArrayUtil::keyIntersect($params, array('eventId', 'contactFieldId'));

		$this->execute($sql, $params, 'Remove all contact field options.');
	}

	/**
	 *
	 * @param array $params [eventId, id, displayName, defaultSelected]
	 */
	public function save($params) {
		$sql = '
			UPDATE
				ContactFieldOption
			SET
				ContactFieldOption.displayName = :displayName,
				ContactFieldOption.defaultSelected = :defaultSelected
			WHERE
				ContactFieldOption.id = :id
			AND
				ContactFieldOption.contactFieldId
			IN (
				SELECT ContactField.id 
				FROM ContactField 
				WHERE ContactField.eventId = :eventId 
			)
		';	

		$params = ArrayUtil::keyIntersect($params, array(
			'eventId', 
			'id', 
			'displayName', 
			'defaultSelected'
			));

			$this->execute($sql, $params, 'Save field option.');
	}

	/**
	 *
	 * @param array $params [eventId, id]
	 */
	public function moveOptionUp($params) {
		$option = $this->find($params);

		$this->moveUp($option, 'contactFieldId', $option['contactFieldId']);
	}

	/**
	 *
	 * @param array $params [eventId, id]
	 */
	public function moveOptionDown($params) {
		$option = $this->find($params);

		$this->moveDown($option, 'contactFieldId', $option['contactFieldId']);
	}
}

?>