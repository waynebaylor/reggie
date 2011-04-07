<?php

/**
 * Front controller for routing URIs. 
 *
 * @author wtaylor
 *
 */
class Traffic
{
	private static $separator = 'q';
	private static $context = '/';
	private static $prefixes = array();
	
	/**
	 * Set the context path, default is '/'. This is used in routing/reversing URIs.
	 * @param string $c the context root/path
	 */
	public static function context($c) {
		self::$context = trim($c, '/');
	}
	
	/**
	 * A mapping of URI prefix to directory. This is used when the URI doesn't have the
	 * complete class name. 
	 * @param array $p the prefixes
	 */
	public static function prefixes($pref) {
		foreach ($pref as $p => $d) {
			$p = trim($p, '/');
			if(!empty($p)) {
				self::$prefixes[$p] = $d;
			}
		}
	}
	
	/**
	 * Set the separator to use between action segment and param segments, default is 'q'.
	 * @param unknown_type $s the segment char(s)
	 */
	public static function separator($s) {
		if(!empty($s)) {
			self::$separator = $s;
		}
	}
	
	/**
	 * Direct the given request URI to the matching class and action method.
	 * @param string $requestUri the request URI
	 */
	public static function route($uri) {
		// convert URI to class, action, and params.
		$info = self::parseUri($uri, self::$prefixes, self::$separator);
		
		// invoke action method with params as arguments.
		$obj = new $info['className'];
		call_user_func_array(array($obj, $info['action']), $info['params']);
	}
	
	/**
	 * Converts the given URI to a class name, action method, and params.
	 * @param string $uri the URI
	 * @param array $prefixes array of URI segment -> dir 
	 * @param string $separator chars to use when determining which segments are params
	 * @throws Exception if URI cannot be converted
	 */
	public static function parseUri($uri, $prefixes, $separator) {
		$uri = trim($uri, '/');
		
		// remove any fragment/anchor and query parameters.
		$uri = preg_replace('/#.*+$/', '', $uri);
		$uri = preg_replace('/\?.*+$/', '', $uri);
		
		// remove context path.
		$uri = preg_replace('/^'.self::$context.'\/?/', '', $uri);
		
		$segments = explode('/', $uri);
		
		// check if we have at least a class and action method.
		if(count($segments) < 2) {
			throw new Exception('Error parsing URI: "'.$uri.'". Must contain at least two segments');
		}
		
		// break uri into pieces and pick out the class name part, action, and params.
		$index = array_search($separator, $segments);
		
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
		
		// break prefix dir into pieces so we can include in class name.
		$dir = self::getDirPrefix($uri, $prefixes);
		$prefixSegments = empty($dir)? array() : explode('/', $dir);
		
		// combine prefix with URI segments.
		$classSegments = array_merge($prefixSegments, $classSegments);
		
		return array(
			'className' => implode('_', $classSegments),
			'action' => $actionMethod[0],
			'params' => $paramSegments
		);
	}
	
	/**
	 * Checks the given URI agains the prefixes defined. If a prefix matches
	 * the beginning of he URI, then the corresponding dir is returned. If no
	 * match is found then an empty string is returned.
	 * @param string $uri
	 */
	public static function getDirPrefix($uri, $prefixes) {
		foreach($prefixes as $p => $d) {
			if(preg_match('/^'.$p.'/', $uri) > 0) {
				return $d;
			}
		}
		
		return '';
	}
}

?>