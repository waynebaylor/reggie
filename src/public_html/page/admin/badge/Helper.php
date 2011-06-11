<?php

class page_admin_badge_Helper
{
	public static function getRegTypes($t) {
		if($t['appliesToAll'] === true) {
			return 'All';
		}
		else {
			$names = array();
			foreach($t['appliesTo'] as $regType) {
				$names[] = "({$regType['code']}) {$regType['description']}";
			}
			
			return implode('<br>', $names);
		}
	}
	
	public static function badgeCellSummaries($template, $selectedCellId) {
		$summaries = array();
				
		foreach($template['cells'] as $cell) {
			$summary = array(
				'id' => $cell['id'],
				'text' => '',
				'selected' => false,
				'cell' => $cell
			);

			if($selectedCellId === $cell['id']) {
				$summary['selected'] = true;	
			}
					
			if($cell['hasBarcode'] === 'T') {
				$summary['text'] = 'Barcode';
			}
			else {
				foreach($cell['content'] as $content) {
					if(empty($content['contactFieldId'])) {
						$summary['text'] .= $content['text'];
					}
					else {
						$summary['text'] .= "<{$content['contactFieldName']}>";
					}
				}
			}
			
			$summaries[] = $summary;
		}
		
		return $summaries;
	}
}