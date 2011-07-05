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
	
	public static function hasCreateReg($report) {
		return !(
			($report['isAllRegToDate'] === 'T') || 
			($report['isOptionCount'] === 'T') || 
			($report['isRegTypeBreakdown'] === 'T')
		);
	}
}

?>