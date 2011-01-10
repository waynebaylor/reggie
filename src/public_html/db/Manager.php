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
}

?>