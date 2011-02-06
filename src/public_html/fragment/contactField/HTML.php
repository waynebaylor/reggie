<?php

class fragment_contactField_HTML
{
	public static function selectByEventId($eventId, $config = array()) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'contactFieldId');		
		$config['value'] = ArrayUtil::getValue($config, 'value', '');
		
		$items = array();
		$fields = db_ContactFieldManager::getInstance()->findTextFieldsByEventId($eventId);
		foreach($fields as $field) {
			if($field['formInput']['id'] == model_FormInput::$TEXT) {
				$items[] = array(
					'label' => $field['displayName'],
					'value' => $field['id']	
				);
			}
		}
		
		$config['items'] = $items;
		
		$html = new HTML();
		return  $html->select($config);
	}
}

?>