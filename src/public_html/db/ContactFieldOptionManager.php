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

	public function find($id) {
		$sql = '
			SELECT
				id,
				contactFieldId,
				displayName,
				defaultSelected,
				displayOrder
			FROM
				ContactFieldOption
			WHERE
				id=:id
			ORDER BY
				displayOrder
		';

		$params = array(
			'id' => $id
		);
		
		return $this->queryUnique($sql, $params, 'Find contact field option.');
	}
	
	public function findByField($field) {
		$sql = '
			SELECT
				id,
				contactFieldId,
				displayName,
				defaultSelected,
				displayOrder
			FROM
				ContactFieldOption
			WHERE
				contactFieldId=:id
			ORDER BY
				displayOrder
		';
		
		$params = array(
			'id' => $field['id']
		);
		
		return $this->query($sql, $params, 'Find contact field options.');
	}
	
	public function createOption($option) {
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

		$params = array(
			'contactFieldId' => $option['contactFieldId'],
			'displayName' => $option['displayName'],
			'defaultSelected' => $option['defaultSelected'],
			'displayOrder' => $this->getNextOrder()
		);
		
		$this->execute($sql, $params, 'Create contact field option.');
	}
	
	public function delete($option) {
		$sql = '
			DELETE FROM
				ContactFieldOption
			WHERE
				id=:id
		';

		$params = array(
			'id' => $option['id']
		);
		
		$this->execute($sql, $params, 'Delete contact field option.');		
	}
	
	public function removeOptions($field) {
		$sql = '
			DELETE FROM
				ContactFieldOption
			WHERE
				contactFieldId=:id
		';

		$params = array(
			'id' => $field['id']
		);
		
		$this->execute($sql, $params, 'Remove all contact field options.');		
	}
	
	public function moveOptionUp($option) {
		$this->moveUp($option, 'contactFieldId', $option['contactFieldId']);
	}
	
	public function moveOptionDown($option) {
		$this->moveDown($option, 'contactFieldId', $option['contactFieldId']);
	}
}

?>