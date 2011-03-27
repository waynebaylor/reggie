<?php

class fragment_reportField_HTML
{
	public static function select($event) {
		$opts = array();
		
		// general registrant info comes first.
		$opts[] = array(
			'label' => 'General',
			'value' => array(
				array(
					'label' => 'Date Registered',
					'value' => 'date_registered'
				),
				array(
					'label' => 'Date Cancelled',
					'value' => 'date_cancelled'
				),
				array(
					'label' => 'Category',
					'value' => 'category'
				),
				array(
					'label' => 'Registration Type',
					'value' => 'registration_type'
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
		
		// payment fields come last.
		$opts[] = array(
			'label' => 'Payment Information',
			'value' => array(
				array(
					'label' => 'Total Cost',
					'value' => 'total_cost'
				),
				array(
					'label' => 'Total Paid',
					'value' => 'total_paid'
				),
				array(
					'label' => 'Remaining Balance',
					'value' => 'remaining_balance'
				)
			)
		);
		
		$html = new HTML();
		return $html->select(array(
			'name' => 'contactFieldId',
			'value' => '',
			'items' => $opts
		));
	}	
}

?>