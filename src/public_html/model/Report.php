<?php

class model_Report
{
	public static function hasSearch($report) {
		return !(
			($report['isPaymentsToDate'] === 'T') || 
			($report['isOptionCount'] === 'T') || 
			($report['isRegTypeBreakdown'] === 'T')
		);
	}
	
	public static function hasSpecialField($report, $fieldName) {
		$specialFields = $report['specialFields'];
		
		foreach($specialFields as $f) {
			if($f['name'] === $fieldName) {
				return true;
			}
		}
		
		return false;
	}
}

?>