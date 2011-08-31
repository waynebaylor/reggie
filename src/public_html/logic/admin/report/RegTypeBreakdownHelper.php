<?php

class logic_admin_report_RegTypeBreakdownHelper
{
	public static function addSpecialInfo($report, $info) {
		$newRows = array();
		$totalCount = 0;
		
		$info['headings'] = array('Registration Type ID', 'Registration Type Name', 'Count');
		
		$values = db_ReportManager::getInstance()->findRegTypeBreakdown($report['eventId']);
		foreach($values as $value) {
			$newRows[] = array(
				'data' => array($value['regTypeId'], $value['regTypeName'], $value['regTypeCount'])
			);
			
			$totalCount += $value['regTypeCount'];
		}
		
		$newRows[] = array(
			'data' => array('Total', '', $totalCount)
		);
		
		$info['rows'] = $newRows;
		
		return $info;
	}
}

?>