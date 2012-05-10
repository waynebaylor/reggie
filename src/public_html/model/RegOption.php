<?php

class model_RegOption
{
	/**
	 * works for both RegOptions and VariableQuantityOptions.
	 * @param $regType [id]
	 * @param $option
	 */
	public static function getPrice($regType, $option) {
		foreach($option['prices'] as $price) {
			// is price visible to reg type?
			if(model_RegOptionPrice::isVisibleTo($price, $regType)) {
				// is current date between start/end date of price?
				$currentDate = time();
				$priceStartDate = strtotime($price['startDate']);
				$priceEndDate = strtotime($price['endDate']);
				if(($currentDate >= $priceStartDate) && ($currentDate < $priceEndDate)) {
					return $price;
				}
			}
		}
		
		return null;
	}
}

?>