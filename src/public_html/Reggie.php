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
		$file = str_replace('_', '/', $c).'.php';
		
		// look in each dir listed in include path.
		foreach(explode(PATH_SEPARATOR, get_include_path()) as $path) { 
			$path = rtrim($path, '/').'/'.$file;
			if(file_exists($path)) {
				require_once $path;

				return; 
			}
		}
		
		throw new Exception("Error loading class: '{$c}'. File not found: '{$file}'.");
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
		
		self::setupErrorHandling();
		self::setupTimezone();
		self::setupApplicationPaths();
		self::setupSessionTimeout();
	}
	
	private static function setupSessionTimeout() {
		ini_set('session.gc_maxlifetime', Config::$SETTINGS['SESSION_TIMEOUT']);
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 1);	
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
		$currPath = dirname(__FILE__);
		
		// add the current dir to the include path.
		if(strpos(get_include_path(), $currPath) === false) {
			set_include_path(get_include_path() . PATH_SEPARATOR . $currPath);
		}
		
		// add external libs.
		$libsPath = $currPath.'/libs';
		$libraryDirs = array('html_purifier', 'css_tidy');

		foreach($libraryDirs as $dir) {
			if(strpos(get_include_path(), $libsPath.'/'.$dir) === false) {
				set_include_path(get_include_path() . PATH_SEPARATOR . $libsPath.'/'.$dir);
			}
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