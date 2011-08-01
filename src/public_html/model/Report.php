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
}

?>