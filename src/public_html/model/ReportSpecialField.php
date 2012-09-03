<?php

class model_ReportSpecialField
{
	public static $DATE_REGISTERED = 'DATE_REGISTERED';
	public static $DATE_CANCELLED = 'DATE_CANCELLED';
	public static $CATEGORY	= 'CATEGORY';
	public static $REGISTRATION_TYPE = 'REGISTRATION_TYPE';
	public static $LEAD_NUMBER = 'LEAD_NUMBER';
	public static $TOTAL_COST = 'TOTAL_COST';
	public static $TOTAL_PAID = 'TOTAL_PAID';
	public static $REMAINING_BALANCE = 'REMAINING_BALANCE';
	
	public static function getDisplayName($field) {
		$names = array(
			self::$DATE_REGISTERED => 'Date Registered',
			self::$DATE_CANCELLED => 'Date Cancelled',
			self::$CATEGORY => 'Category',
			self::$REGISTRATION_TYPE => 'Registration Type',
			self::$LEAD_NUMBER => 'Lead Number',
			self::$TOTAL_COST => 'Total Cost',
			self::$TOTAL_PAID => 'Total Paid',
			self::$REMAINING_BALANCE => 'Remaining Balance'
		);
		
		return $names[$field];
	}
	
}

?>