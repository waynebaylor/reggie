<?php

class RequestUtil
{
	/**
	 * Retruns the request scope value associated with the
	 * given key name. if the value is an array, then the 
	 * first value is returned. if there is no key with 
	 * the given name, then the given default value is returned.
	 * 
	 * @param $name the key name
	 * @param $default the default value
	 */
	public static function getValue($name, $default) {
		$value = ArrayUtil::getValue($_REQUEST, $name, $default);
		return is_array($value)? $value[0] : $value;		
	}
	
	/**
	 * Returns the request scope array associated with the
	 * given key name. if there is only one value associated 
	 * with the name, then it is returned as an array. if 
	 * the is no key with the given name, then the given 
	 * default array is returned.
	 * 
	 * @param $name the key name
	 * @param $defaults the default array
	 */
	public static function getValueAsArray($name, $defaults) {
		if(isset($_REQUEST[$name])) {
			$value = $_REQUEST[$name];
			return is_array($value)? $value : array($name => $value); 
		}
		
		return $defaults;
	}
	
	/**
	 * Returns the intersection of the request parameters 
	 * and the given names. Basically it returns the given 
	 * subset of request parameters.
	 * 
	 * @param $paramNames
	 */
	public static function getParameters($paramNames) {
		return ArrayUtil::keyIntersect($_REQUEST, $paramNames);
	}
}

?>