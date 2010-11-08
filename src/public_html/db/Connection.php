<?php

class db_Connection extends PDO
{
	function __construct() {
		$config = array(
			'mysql:host='.Config::$SETTINGS['DB_HOST'],
			'port=3306;dbname='.Config::$SETTINGS['DB_NAME']
		);
		
		parent::__construct(
			implode(';', $config), 
			Config::$SETTINGS['DB_USERNAME'], 
			Config::$SETTINGS['DB_PASSWORD'],
			array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'set names utf8'
			));
			
		$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}

?>