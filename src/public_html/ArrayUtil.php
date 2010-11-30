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
}

?>