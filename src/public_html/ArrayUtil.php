<?php

class ArrayUtil
{
	/**
	 * Returns the key/value pairs from 'array' where the keys are 
	 * in the array 'keys'. 
	 */
	public static function keyIntersect($array, $keys) {
		$params = array();

		foreach($array as $key => $value) {
			if(in_array($key, $keys)) {
				$params[$key] = $value; 
			}	
		}
		
		return $params;
	}
	
	/**
	 * Returns the key/value pairs from 'array' where the keys match
	 * the given regular expression.
	 */
	public static function keyMatches($array, $regex) {
		$params = array();
		
		foreach($array as $key => $value) {
			if(preg_match($regex, $key)) {
				$params[$key] = $value;
			}
		}
		
		return $params;
	}
	
	/**
	 * Retruns the value associated with the
	 * given key name. if there is no key with 
	 * the given name, then the given default value is returned.
	 * 
	 * @param $arr the array
	 * @param $name the key name
	 * @param $default the default value
	 */
	public static function getValue($arr, $name, $default) {
		return isset($arr[$name])? $arr[$name] : $default;
	}
}

?>