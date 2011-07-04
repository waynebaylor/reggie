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
					if($content['showRegType'] === 'T') {
						$summary['text'] = '<Registration Type>';
					}
					else if($content['showLeadNumber'] === 'T') {
						$summary['text'] = '<Lead Number>';
					}
					else if(empty($content['contactFieldId'])) {
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
	
	public static function selectFields($event) {
		$opts = array();
		
		$opts[] = array(
			'label' => 'General',
			'value' => array(
				array(
					'label' => 'Registration Type',
					'value' => 'registration_type'
				),
				array(
					'label' => 'Lead Number',
					'value' => 'lead_number'
				)
			)
		);
		
		// group all the information fields by section id.
		$sectionFields = array();
		
		$fields = model_Event::getInformationFields($event);
		foreach($fields as $field) {
			$section = model_Event::getSectionById($event, $field['sectionId']);
			
			if(empty($sectionFields[$section['id']])) {
				$sectionFields[$section['id']] = array();
			}
			
			$sectionFields[$section['id']][] = array(
				'label' => $field['displayName'],
				'value' => $field['id']
			);
		}
		
		foreach($sectionFields as $sectionId => $fields) {
			$section = model_Event::getSectionById($event, $sectionId);
			$opts[] = array(
				'label' => $section['name'],
				'value' => $fields
			);
		}
		
		$html = new HTML();
		return $html->select(array(
			'name' => 'templateField',
			'value' => '',
			'items' => $opts
		));		
	}
}