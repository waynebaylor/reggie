<?php

class Config
{
	public static $MODE_DEVELOPMENT = 'development';
	public static $MODE_SHOW_SQL = 'show-sql';
	
	public static $SETTINGS = array(
		
		//
		// the "mode(s)" in which the application is being run. valid values are
		// 'development', 'show-sql'. this is used to determine what to
		// log in the Logger class, etc. 
		//
		'MODE' => array('development'),

		//
		// user session timeout. 30 mins.
		//
		'SESSION_TIMEOUT' => 1800,
		
		//
		// timezone setting for date/time functions.
		//
		'TIMEZONE' => 'America/New_York',

		//
		// log file errors are written to.
		//
		'ERROR_LOG' => '/var/www/reggie/hhreg.error',
	
		//
		// payment information is written to this file.
		//
		'PAYMENT_LOG' => '/var/www/reggie/hhreg.error',
	
		//
		// database properties
		//
		'DB_HOST'     => 'localhost',
		'DB_NAME'     => 'reggie',
		'DB_USERNAME' => 'reggie',
		'DB_PASSWORD' => 'reggie',
	
		//
		// the directory from which all actions are autoloaded.
		'ACTION_BASE' => 'action',
		
		//
		// Authorize.NET properties.
		//
		// (login: 22veM2L8, trans key: 6KpW2t768G6GZ6b5) 
		'AUTH_NET_TEST_URL' => 'https://test.authorize.net/gateway/transact.dll',
		'AUTH_NET_URL' => 'https://secure.authorize.net/gateway/transact.dll'
	);
}

?>