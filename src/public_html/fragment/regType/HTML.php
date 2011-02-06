<?php

class fragment_regType_HTML
{
	public static function selectByEventId($eventId, $config = array()) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'regTypeIds[]');
		$config['value'] = ArrayUtil::getValue($config, 'value', '');
		$config['multiple'] = ArrayUtil::getValue($config, 'multiple', 'multiple');
		$config['size'] = ArrayUtil::getValue($config, 'size', 5);
		
		$items = array();
		
		if(isset($config['multiple'])) {
			$items[] = array(
				'label' => 'All',
				'value' => '-1'
			);	
		}
		
		$regTypes = db_RegTypeManager::getInstance()->findByEventId($eventId);
		foreach($regTypes as $regType) {
			$items[] = array(
				'label' => "({$regType['code']}) {$regType['description']}",
				'value' => $regType['id']
			);
		}
		
		$config['items'] = $items;
		
		$html = new HTML();
		return $html->select($config);
	}
}

?>