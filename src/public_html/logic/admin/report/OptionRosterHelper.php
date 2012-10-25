<?php

class logic_admin_report_OptionRosterHelper
{
	public static function addSpecialInfo($report, $info) {
		$newRows = array();
		
		$info['headings'] = array('Option Code', 'Quantity', 'Last Name', 'First Name');
		
		$values = db_ReportManager::getInstance()->regOptionRoster(array('eventId' => $report['eventId']));
		foreach($values as $value) {
			$newRows[] = array(
				'data' => array(
					$value['optionCode'],
					$value['quantity'],
					$value['lastName'],
					$value['firstName']
				),
				'regGroupId' => $value['regGroupId']
			);
		}
		
		// overwrite with augmented data.
		$info['rows'] = $newRows;
		
		return $info;
	}
	
}

?>