<?php

class model_RegOptionPrice
{
	public static function isVisibleTo($price, $regType) {
		if($price['visibleToAll']) {
			return true;
		}
		
		foreach($price['visibleTo'] as $type) {
			if(intval($type['id'], 10) === intval($regType['id'], 10)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * returns whether or not the two prices have overlapping dates, assuming they are both
	 * visible to at least one common reg type. 
	 * @param $regType
	 * @param $price1
	 * @param $price2
	 */
	public static function hasOverlap($option, $priceInfo) {
		foreach($option['prices'] as $existingPrice) {
			// don't compare the price to itself.
			if(empty($priceInfo['id']) || (intval($existingPrice['id'], 10) !== intval($priceInfo['id'], 10))) { 
				$dateOverlap = self::hasDateOverlap($existingPrice, $priceInfo);
				$visibilityOverlap = self::hasVisibilityOverlap($existingPrice, $priceInfo['regTypeIds']);

				if($dateOverlap && $visibilityOverlap) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * check if the date range for two prices overlap.
	 * @param $price1
	 * @param $price2
	 */
	private static function hasDateOverlap($price1, $price2) {
		$start1 = strtotime($price1['startDate']);
		$end1 = strtotime($price1['endDate']);
		
		$start2 = strtotime($price2['startDate']);
		$end2 = strtotime($price2['endDate']);

		$startOverlap = ($start2 >= $start1) && ($start2 < $end1);
		$endOverlap = ($end2 > $start1) && ($end2 < $end1);
		
		return $startOverlap || $endOverlap;
	}
	
	/**
	 * check if the given price is visible to any of the given reg type IDs.
	 * @param $price
	 * @param $regTypeIds
	 */
	private static function hasVisibilityOverlap($price, $regTypeIds) {
		// -1 means ALL reg types.
		if(in_array('-1', $regTypeIds)) {
			return true;
		}
		
		// if not visible to ALL, then check individual reg types.
		foreach($regTypeIds as $regTypeId) {
			// simulate a reg type object.
			$r = array('id' => $regTypeId);
			
			if(self::isVisibleTo($price, $r)) {
				return true;
			}
		}	

		return false;
	}
}

?>