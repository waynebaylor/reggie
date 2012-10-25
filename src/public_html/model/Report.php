<?php

class model_Report
{
	public static $PAYMENTS_TO_DATE = 'PAYMENTS_TO_DATE';
	public static $ALL_REG_TO_DATE = 'ALL_REG_TO_DATE';
	public static $OPTION_COUNTS = 'OPTION_COUNTS';
	public static $REG_TYPE_BREAKDOWN = 'REG_TYPE_BREAKDOWN';
	public static $BADGE_REPORT = 'BADGE_REPORT';
	public static $OPTION_ROSTER = 'OPTION_ROSTER';
	public static $STANDARD = 'STANDARD';
	
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