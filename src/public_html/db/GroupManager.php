<?php

abstract class db_GroupManager extends db_OrderableManager
{
	protected function __construct() {
		parent::__construct();
	}
	
	protected function populate(&$obj, $arr) {
		parent::populate($obj, $arr);

		$obj['options'] = db_RegOptionManager::getInstance()->findByGroup($obj);
		
		return $obj;
	}
	
	protected abstract function createMappingRow($group);
	
	public abstract function find($id);
	
	public abstract function moveGroupUp($group);
	
	public abstract function moveGroupDown($group);
	
	public function createGroup($group) {
		$sql = '
			INSERT INTO
				RegOptionGroup(
					description,
					required,
					multiple,
					minimum,
					maximum
				)
			VALUES(
				:description,
				:required,
				:multiple,
				:minimum,
				:maximum
			)
		';
		
		$params = array(
			'description' => $group['description'],
			'required'    => $group['required'],
			'multiple'    => $group['multiple'],
			'minimum'     => $group['minimum'],
			'maximum'     => $group['maximum']	
		);
		
		$this->execute($sql, $params, 'Create reg option group.');
		
		// create mapping row
		$this->createMappingRow($group);
	}
	
	public function delete($group) {
		$sql = '
			DELETE FROM
				RegOptionGroup
			WHERE
				id=:id
		';
		
		$params = array(
			'id' => $group['id']
		);
		
		$this->execute($sql, $params, 'Delete reg option group.');
	}
	
	public function save($group) {
		$sql = '
			UPDATE
				RegOptionGroup
			SET
				description=:description,
				required=:required,
				multiple=:multiple,
				minimum=:minimum,
				maximum=:maximum
			WHERE
				id=:id
		';
		
		$params = array(
			'id'          => $group['id'],
			'description' => $group['description'],
			'required'    => $group['required'],
			'multiple'    => $group['multiple'],
			'minimum'     => $group['minimum'],
			'maximum'     => $group['maximum']
		);
		
		$this->execute($sql, $params, 'Save section reg option group.');
	}
}

?>