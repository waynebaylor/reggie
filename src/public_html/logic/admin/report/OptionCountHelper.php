<?php

class logic_admin_report_OptionCountHelper
{
	public static function addSpecialInfo($report, $info) {
		$newRows = array();
		
		$info['headings'] = array('Option Code', 'Option Name', 'Price Name', 'Option Price', 'Count', 'Expected Revenue');
		
		$values = db_ReportManager::getInstance()->findOptionCounts($report);
		foreach($values as $value) {
			$newRows[] = array(
				'data' => array(
					$value['optionCode'],
					$value['optionName'],
					$value['priceName'],
					'$'.number_format($value['price'], 2),
					$value['priceCount'],
					'$'.number_format($value['revenue'], 2)
				)
			);
		}
		
		$info['rows'] = $newRows;
		
		return $info;
	}	
}

?>