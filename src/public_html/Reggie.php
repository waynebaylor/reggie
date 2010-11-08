<?php

class Reggie
{
	// application context path. e.g. "/apps/reggie" is the context path 
	// if the application were located in "/var/www/apps/reggie". 
	public static $CONTEXT;
	
	// the path to the application root. e.g. "/var/www/apps/reggie" would
	// be the path to the application.
	public static $PATH;
	
	
	// passed to set_error_handler().
	public static function errorConverter($errno, $errstr, $errfile, $errline ) {
		throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
	}
	
	// passed to spl_autoload_register(). replaces underscore 
	// with slash to convert a class name to a file path.
	public static function autoload($c) { 
		require_once str_replace('_', '/', $c).'.php';
	}
	
	// do any application specific initialization.
	public static function setup() {
		// these need to run first since they initialize the
		// 'classpath' and the auto class loading.
		self::setupIncludePath();
		self::setupClassLoading();
		
		self::setupTimezone();
		self::setupErrorHandling();
		self::setupApplicationPaths();
	}
	
	private static function setupTimezone() {
		// set default timezone.
		date_default_timezone_set(Config::$SETTINGS['TIMEZONE']);
	}
	
	private static function setupErrorHandling() {
		// runtime properties.
		ini_set('display_errors', '0');
		ini_set('log_errors', '1');
		ini_set('error_log', Config::$SETTINGS['ERROR_LOG']);
		
		error_reporting(E_ALL | E_STRICT);
		set_error_handler(array('Reggie', 'errorConverter'));
	}
	
	private static function setupIncludePath() {
		// add the current dir to the include path.
		if(strpos(get_include_path(), dirname(__FILE__)) === false) {
			set_include_path(get_include_path() . PATH_SEPARATOR . dirname(__FILE__));
		}
	}
	
	private static function setupClassLoading() {
		// set the class autoloading function. 
		spl_autoload_register(array('Reggie', 'autoload'));
	}
	
	private static function setupApplicationPaths() {
		// set the path to the application root.
		Reggie::$PATH = dirname(__FILE__);
		
		// set the context path. all requests are sent to Controller, so
		// we know the path will end with 'Controller.php'.
		Reggie::$CONTEXT = str_replace('Controller.php', '', $_SERVER['SCRIPT_NAME']);
	}
}

?>