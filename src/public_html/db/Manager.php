<?php

abstract class db_Manager
{
	// format for dates stored in the database.
	public static $DATE_FORMAT = 'Y-m-d H:i';
	
	protected static $conn;

	protected function __construct() {
		if(empty(self::$conn)) {
			self::$conn = new db_Connection();
		}
	}
	
	/**
	 * query the database.
	 * @param string $sql the SQL string
	 * @param array $params the parameters to bind in the prepared statement
	 * @param string $desc description of SQL
	 */
	protected function rawQuery($sql, $params, $desc) {
		// prefix parameters with colons if they aren't already.
		foreach($params as $key => $value) {
			if(strpos($key, ':') === false) {
				$newKey = ':'.$key;
				$params[$newKey] = $value;
				unset($params[$key]);
			}
		}
		
		$ps = self::$conn->prepare($sql);

		$success = $ps->execute($params);

		Logger::logSql($ps->queryString, $params, $desc, $success);

		if($success) {
			return $ps->fetchAll();
		}
		else {
			throw new Exception('Error executing SQL: '.$ps->queryString);
		}
	}
	
	/**
	 * same as query, except that only a single result is returned.
	 * @param string $sql the SQL string
	 * @param array $params the parameters to bind in the prepared statement
	 * @param string $desc description of SQL
	 */
	protected function rawQueryUnique($sql, $params, $desc) {
		$results = $this->rawQuery($sql, $params, $desc);
		
		if(empty($results)) {
			return NULL;
		}
		else {
			return $results[0];
		}
	}

	/**
	 * same as rawQuery() except that the populate() method is
	 * also run for each result.
	 * @param string $sql
	 * @param array $params
	 * @param string $desc
	 */
	protected function query($sql, $params, $desc) {
		$results = $this->rawQuery($sql, $params, $desc);
		
		$objs = array();

		if(!empty($results)) {
			foreach($results as $result) {
				$obj = array();
				$objs[] = $this->populate($obj, $result);
			}
		}

		return $objs;
	}
	
	/**
	 * same as rawQueryUnique() except that the populate() method
	 * is also run for the result.
	 * @param string $sql
	 * @param array $params
	 * @param string $desc
	 */
	protected function queryUnique($sql, $params, $desc) {
		$results = $this->query($sql, $params, $desc);
		
		if(empty($results)) {
			return NULL;
		}
		else {
			return $results[0];
		}
	}
	
	/**
	 * runs the given SQL, but does not return anything.
	 * @param string $sql the SQL string
	 * @param array $params the parameters to bind in the prepared statement
	 * @param string $desc description of SQL
	 */
	protected function execute( $sql, $params, $desc) {
		// prefix parameters with colons if they aren't already.
		foreach($params as $key => $value) {
			if(strpos($key, ':') === false) {
				$newKey = ':'.$key;
				$params[$newKey] = $value;
				unset($params[$key]);
			}
		}
		
		$ps = self::$conn->prepare($sql);

		$success = $ps->execute($params);

		Logger::logSql($ps->queryString, $params, $desc, $success);
		
		if(!$success) {
			throw new Exception('Error executing SQL: '.$ps->queryString);
		}
	}
	
	/**
	 * returns the last insert id. useful for getting the assigned id
	 * after executing an INSERT statement.
	 */
	protected function lastInsertId() {
		return self::$conn->lastInsertId();
	}
	
	/**
	 * populates the given array with the appropriate 
	 * fields. default implementation is to run ObjectUtils::populate().
	 * @param array $obj
	 */
	protected function populate(&$obj, $arr) {
		return ObjectUtils::populate($obj, $arr);
	}
	
	public function beginTransaction() {
		self::$conn->beginTransaction();
	}
	
	public function commitTransaction() {
		self::$conn->commit();
	}
	
	public function rollbackTransaction() {
		self::$conn->rollBack();
	}
	
	/**
	 * Creates and runs an SQL insert statement based on the given
	 * table name, parameters, and values. For example the given data
	 * would generate the following SQL:
	 * 
	 * <pre>...insert('StaticPage', array(
	 * 	'eventId' => 3,
	 * 	'name' => 'about_us',
	 * 	'title' => 'About Us Page',
	 * 	'content' => 'Hello and welcome to our new page!'
	 * ));
	 * </pre>
	 * 
	 * <pre>
	 * 	INSERT INTO
	 * 		StaticPage(
	 * 			eventId,
	 * 			name,
	 * 			title,
	 * 			content
	 * 		)
	 * 	VALUES(
	 * 		:eventId,
	 * 		:name,
	 * 		:title,
	 * 		:content
	 * 	)
	 * </pre>
	 * 
	 * @param string $table the table name
	 * @param array $values the column names and values
	 */
	protected function insert($table, $values) {
		$columnNames = implode(',', array_keys($values));
		
		$fields = array();
		foreach($values as $columnName => $_) {
			$fields[] = ":{$columnName}";
		}
		$fields = implode(',', $fields);
		
		$sql = "INSERT INTO {$table}( {$columnNames} ) VALUES( {$fields} )";
		
		$this->execute($sql, $values, "Insert into {$table}.");
	}
	
	/**
	 * Updates the values in the given table based on the given 
	 * conditions (interpreted as equality conditions).
	 * 
	 * @param string $table the table name
	 * @param array $values the column names and values
	 * @param array $restrictions the column equality conditions
	 */
	protected function update($table, $values, $restrictions) {
		$fields = array();
		foreach($values as $columnName => $_) {
			$fields[] = "{$columnName} = :{$columnName}";
		}
		$fields = implode(',', $fields);
		
		$conditions = array();
		foreach($restrictions as $columnName => $_) {
			$conditions[] = "{$columnName} = :{$columnName}";
		}
		$conditions = implode(' AND ', $conditions);
		
		$sql = "UPDATE {$table} SET {$fields}";
		if(!empty($conditions)) {
			$sql .= " WHERE {$conditions}";
		}
		
		$this->execute($sql, array_merge($values, $restrictions), "Update {$table}.");
	}
	
	/**
	 * Delete rows from the given table based on the given conditions
	 * (interpreted as equality conditions).
	 * 
	 * @param string $table the table name
	 * @param array $restrictions the column equality conditions
	 */
	protected function del($table, $restrictions) {
		$conditions = array();
		foreach($restrictions as $columnName => $_) {
			$conditions[] = "{$columnName} = :{$columnName}";
		}
		$conditions = implode(' AND ', $conditions);
		
		$sql = "DELETE FROM {$table}";
		if(!empty($conditions)) {
			$sql .= " WHERE {$conditions}";
		}
		
		$this->execute($sql, $restrictions, "Delete from {$table}.");
	}
	
	/**
	 * Query the given table based on the given conditions (interpreted
	 * as equality conditions). If raw results are needed, then do not 
	 * use this method; use selectRaw() instead.
	 * 
	 * @param string $table the table name
	 * @param array $fields the column names
	 * @param array $restrictions the equality conditions
	 */
	protected function select($table, $fields, $restrictions) {
		$columnNames = implode(',', $fields);
		
		$conditions = array();
		foreach($restrictions as $columnName => $_) {
			$conditions[] = "{$columnName} = :{$columnName}";
		}
		$conditions = implode(' AND ', $conditions);
		
		$sql = "SELECT {$columnNames} FROM {$table}";
		if(!empty($conditions)) {
			$sql .= " WHERE {$conditions}";
		}
		
		return $this->query($sql, $restrictions, "Find from {$table}.");
	}
	
	/**
	 * Same as select(), but returns raw result sets, like rawQuery().
	 * 
	 * @param string $table the table name
	 * @param array $fields the column names
	 * @param array $restrictions the equality conditions
	 */
	protected function rawSelect($table, $fields, $restrictions) {
		$columnNames = implode(',', $fields);
		
		$conditions = array();
		foreach($restrictions as $columnName => $_) {
			$conditions[] = "{$columnName} = :{$columnName}";
		}
		$conditions = implode(' AND ', $conditions);
		
		$sql = "SELECT {$columnNames} FROM {$table}";
		if(!empty($conditions)) {
			$sql .= " WHERE {$conditions}";
		}
		
		return $this->rawQuery($sql, $restrictions, "Find from {$table}.");
	}
	
	protected function selectUnique($table, $fields, $restrictions) {
		$r = $this->select($table, $fields, $restrictions);
		
		if(empty($r)) {
			return NULL;
		}
		else {
			return $r[0];
		}
	}
}

?>