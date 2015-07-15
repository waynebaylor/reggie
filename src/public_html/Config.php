<?php

class Config
{
	// uses test authorize.net url
	public static $MODE_DEVELOPMENT = 'development';

	// logs sql.
	public static $MODE_SHOW_SQL = 'show-sql';

	// use https instead of http where applicable. for full SSL enforcement the .htaccess file must be configured too.
	public static $MODE_SSL = 'ssl'; 
	
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
		'ERROR_LOG' => '/tmp/reggie_error.log',
	
		//
		// payment information is written to this file.
		//
		'PAYMENT_LOG' => '/tmp/reggie_error.log',
	
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
		'AUTH_NET_TEST_URL' => 'https://test.authorize.net/gateway/transact.dll',
		'AUTH_NET_URL' => 'https://secure.authorize.net/gateway/transact.dll'
	);
}

?>
