<?php

class model_EmailTemplate
{
	public static function hasOverlap($template, $regTypeIds) {
		if($template['availableToAll'] || in_array(-1, $regTypeIds)) {
			return true;
		}
		else {
			foreach($template['availableTo'] as $regType) {
				if(in_array($regType['id'], $regTypeIds)) {
					return true;
				}
			}
		}
		
		return false;
	}
}

?>