<?php

class Reggie
{
	/**
	 * application context path. e.g. "/apps/reggie" is the context path 
	 * if the application were located in "/var/www/apps/reggie". it will 
	 * always start with a '/'.
	 */
	public static $CONTEXT;
	
	/**
	 * the path to the application root. e.g. "/var/www/apps/reggie" would
	 * be the path to the application.
	 */
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
	
	/**
	 * converts a url into an action class name. given the url '/admin/event/EditEvent',
	 * the class name 'action_admin_event_EditEvent' would be returned.
	 */
	public static function actionClass($url) {
		// remove leading slash, since we assume a full path.
		$url = ltrim($url, '/');

		// if url doesn't start with action base, then prefix it 
		// before converting to a class name.
		if(strpos($url, Config::$SETTINGS['ACTION_BASE']) !== 0) {
			$url = Config::$SETTINGS['ACTION_BASE'].'/'.$url;
		}	
		
		return str_replace('/', '_', $url);
	}
	
	/**
	 * prefixes the given url with the applications context root. this is useful
	 * because the application can reference urls as if it were running in the
	 * root dir ('/').
	 */
	public static function contextUrl($url) { 
		if(strpos($url, self::$CONTEXT) !== 0) {
			return str_replace('//', '/', self::$CONTEXT.'/'.ltrim($url, '/'));
		}
		else {
			return $url;
		} 
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
		
		// set the context path. all requests are sent to index.php, so
		// we know the path will end with 'index.php'.
		Reggie::$CONTEXT = '/'.trim(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '/');
	}
}

?>