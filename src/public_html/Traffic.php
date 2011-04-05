<?php

/**
 * Interface for Traffic controllers.
 * @author wtaylor
 *
 */
interface TrafficController
{
	/**
	 * Process the given URI. 
	 * @param string $uri the cleaned-up URI 
	 * @param array $match any regular expression matches
	 */
	public function handle($uri, $matches);
	
	/**
	 * Convert the given URI info into a URI.
	 * @param string $context the context root/path
	 * @param string $path the URI path
	 * @param string $params named parameters
	 */
	public function reverse($context, $path, $params);
}

/**
 * Controller that maps URIs to classes and invokes an action method. The URI
 * contains the path to the class, the action method, and any parameter values.
 * For example:
 * <ul>
 *  <li> '/path/to/class/exec/q/val1/val2' is mapped to: path_to_Class->exec(val1, val2)</li>
 * </ul>  
 * 
 * @author wtaylor
 *
 */
class ConventionalController implements TrafficController
{
	public function __construct($dirPrefix = '', $separator = 'q') {
		$this->prefix = $dirPrefix;
		$this->separator = $separator;
	}
	
	public function handle($uri, $matches) {
		$info = $this->parseUri($uri);
		
		$obj = new $info['className'];
		call_user_func_array(array($obj, $info['action']), $info['params']);
	}
	
	public function reverse($context, $path, $params) {
		
	}
	
	public function parseUri($uri) {
		$segments = explode('/', $uri);
		
		if(count($segments) < 2) {
			throw new Exception('Error parsing URI: "'.$uri.'". Must contain at least two segments');
		}
		
		if(!empty($this->prefix)) {
			array_unshift($segments, $this->prefix);
		}
		
		$index = array_search($this->separator, $segments);
		
		if($index !== false) {
			$classSegments = array_slice($segments, 0, $index-1);
			$actionMethod = array_slice($segments, $index-1, 1);
			$paramSegments = array_slice($segments, $index);
			array_shift($paramSegments);
		}
		else {
			$classSegments = array_slice($segments, 0, -1);
			$actionMethod = array_slice($segments, -1, 1);
			$paramSegments = array();
		}
		
		// upper-case the last segment in the class name.
		$classSegments[] = ucfirst(array_pop($classSegments));
		
		return array(
			'className' => implode('_', $classSegments),
			'action' => $actionMethod[0],
			'params' => $paramSegments
		);
	}
}

////////////////////////////////////////////////////////////////

/**
 * Front controller for routing URIs. 
 *
 * @author wtaylor
 *
 */
class Traffic
{
	private static $context = '/';
	private static $routes = array();
	
	/**
	 * Set the context path, default is '/'. This is used in routing/reversing URIs.
	 * @param string $c the context root/path
	 */
	public static function context($c) {
		self::$context = trim($c, '/');
	}
	
	/**
	 * Assign a controller to handle URIs that match the given pattern. The pattern can
	 * be a regular expression if you follow the two rules:
	 * <ul>
	 * <li>Omit the surrounding delimiters. '/the(.*)pattern/' should be written as 'the(.*)pattern'.</li>
	 * <li>Omit the start/end anchors: '^/some/path/\d{3}$' should be written as '/some/path/\d{3}'.</li>
	 * <li>Do not escape forward slashes: '/this/pattern/(\d+)/is/okay'.</li>
	 * @param string $pattern the URI pattern to match
	 * @param TrafficController $controller the instance that will handle matching requests
	 */
	public static function route($pattern, $controller) {
		if(!empty($pattern) && isset($controller)) {
			$pattern = trim($pattern, '/');
			$segments = explode('/', $pattern);
			
			$r = new stdClass();
			$r->regex = '/^'.implode('\/', $segments).'/';
			$r->controller = $controller;
			
			self::$routes[] = $r;
		}
	}
	
	/**
	 * Direct the given request URI to the matching controller for processing.
	 * @param string $requestUri the request URI
	 * @throws Exception if no match is made
	 */
	public static function direct($requestUri) {
		$uri = trim($requestUri, '/');
		
		// remove any fragment/anchor and query parameters.
		$uri = preg_replace('/#.*+$/', '', $uri);
		$uri = preg_replace('/\?.*+$', '', $uri);
		
		// remove context path.
		$uri = preg_replace('/^'.self::$context.'\/?/', '', $uri);
		
		foreach(self::$routes as $route) {
			$matches = array();
			$isMatch = preg_match($route->regex, $uri, $matches);
			if($isMatch > 0) {
				$route->controller->handle($uri, $matches);
				return;
			}
		}
		
		throw Exception('No route found for URI: '.$requestUri);
	}
	
	/**
	 * Convert the given path and parameters into a URI. 
	 * @param string $path the path
	 * @param array $params the parameters
	 * @throws Exception if a match is not made
	 */
	public static function reverse($path, $params) {
		foreach(self::$routes as $route) {
			if(preg_match($route->regex, $path)) {
				return $route->controller->reverse(self::$context, $path, $params);
			}
		}
		
		throw new Exception('No route found for link path: '.$path);
	}
}

?>