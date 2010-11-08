<?php

class ObjectUtils
{
	/**
	 * Set obj array properties with values from the                       TODO -- may be able to replace some code 
	 * given array. Properties are mapped as follows:                              with array_merge_recursive() --
	 * 
	 *  ['prop'] -> ['prop']
	 *  ['nested_prop'] -> ['nested']['prop']
	 *  ['another_prop_nested'] -> ['another']['prop']['nested']
	 *   
	 * @param array $arr the array of values
	 * @param array $obj the object to populate
	 */
	public static function populate(&$obj, $arr) {
		foreach($arr as $key => $value) {
			// only copy values from non-numeric keys.
			if(!is_numeric($key)) {
				$fields = explode('_', $key); 
				ObjectUtils::setProperty($obj, $fields, $value);
			}
		}
		
		return $obj;
	}
	
	private static function setProperty(&$arr, $keys, $value) {
		$size = count($keys);
		$current = &$arr;
		foreach($keys as $index => $key) {
			// if it's the last key, then set the given value.
			if(intval($index, 10) === (intval($size, 10)-1)) {
				if(isset($current[$key])) {
					// if it is already an array, then add
					// this value to it.
					if(is_array($current[$key])) {
						$current[$key][] = $value;
					}
					// otherwise, copy the existing value
					// and this value to an array and set
					// the property equal to the new array.
					else {
						$existingValue = $current[$key];
						$current[$key] = array(
							$existingValue,
							$value
						);
					}
				}
				else {
					$current[$key] = $value;
				}
			}
			// otherwise, keep building up the path to the final field.
			else {
				// we are assuming that if the field is already
				// set then it is set as an array.
				if(!isset($current[$key])) {
					$current[$key] = array();
				}
			}
			
			// update current to the next field.
			$current = &$current[$key];
		}
		
		return $arr;
	}
	
	/**
	 * returns the nested property value. for example, if $keyArr = array('one', 'two', 'three'),
	 * then this would return $arr['one']['two']['three'] or NULL if the property doesn't exist. 
	 * @param array $arr
	 * @param array $keyArr
	 */
	private static function getProperty($arr, $keyArr) {
		$prop;

		foreach($keyArr as $key) {
			if(isset($arr[$key])) {
				$prop = $arr[$key];
			}
			else {
				return NULL;
			}
		}
		
		return $prop;
	}
}

?>