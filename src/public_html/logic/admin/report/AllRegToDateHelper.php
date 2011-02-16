<?php

class logic_admin_report_AllRegToDateHelper
{
	public static function addSpecialInfo($report, $info) {
		$newRows = array();

		$info['headings'] = array_merge(
			array('Reg Group ID', 'Registration ID', 'Registration Type Code'), 
			$info['headings']
		);
		$info['headings'] = array_merge(
			$info['headings'], 
			array('Option Code', 'Date Added', 'Date Cancelled', 'Option Name', 'Price Name', 'Option Price', 'Quantity')
		);
	
		$values = db_ReportManager::getInstance()->findAllRegToDateValues($report['eventId']);
		foreach($values as $value) {
			$augmentedRow = array();
			
			foreach($info['rows'] as $row) {
				if($row['registrationId'] == $value['regId']) {
					$augmentedRow = array_merge(
						array($value['regGroupId'], $value['regId'], $value['regTypeCode']), 
						$row['data']
					);
					break;
				}	
			}
			
			$augmentedRow = array_merge($augmentedRow, array(
				$value['optionCode'], $value['dateAdded'], $value['dateCancelled'], 
				$value['optionName'], $value['priceName'], '$'.number_format($value['price'], 2), $value['quantity']
			));
			
			$newRows[] = array(
				'regGroupId' => $value['regGroupId'],
				'registrationId' => $value['regId'],
				'data' => $augmentedRow
			);
		}
		
		// overwrite with augmented data.
		$info['rows'] = $newRows;
		
		return $info;
	}
}

?>