<?php

class ArrayUtil
{
	/**
	 * Returns the key/value pairs from 'arr' where the keys are 
	 * in the array 'keys'. This is different from array_intersect* functions
	 * in that the first argument is an associative array and the second is 
	 * a simple array.
	 * 
	 * @param array $arr the key/value pairs
	 * @param array $keys the keys to keep
	 */
	public static function keyIntersect($arr, $keys) { 
		$params = array();

		foreach($arr as $key => $value) {
			if(in_array($key, $keys, true)) {  
				$params[$key] = $value; 
			}	
		}

		return $params;
	}
	
	/**
	 * Returns the key/value pairs from 'array' where the keys match
	 * the given regular expression.
	 */
	public static function keyMatches($arr, $regex) {
		$params = array();
		
		foreach($arr as $key => $value) {
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
	
	public static function getValues($arr, $nameDefaults) {
		$values = array();
		
		foreach($nameDefaults as $name => $default) {
			$values[$name] = self::getValue($arr, $name, $default);
		}
		
		return $values;
	}
}

?>