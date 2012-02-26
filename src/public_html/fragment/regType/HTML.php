<?php

class fragment_regType_HTML
{
	public static function selectByEventId($eventId, $config = array()) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'regTypeIds[]');
		$config['value'] = ArrayUtil::getValue($config, 'value', '');
		$config['multiple'] = ArrayUtil::getValue($config, 'multiple', true);
		$config['size'] = ArrayUtil::getValue($config, 'size', 5);
		
		$items = array();
		
		if($config['multiple']) {
			$items[] = array(
				'label' => 'All',
				'value' => '-1'
			);	
		}
		
		$regTypes = db_RegTypeManager::getInstance()->findByEventId(array('eventId' => $eventId));
		foreach($regTypes as $regType) {
			$items[] = array(
				'label' => "({$regType['code']}) {$regType['description']}",
				'value' => $regType['id']
			);
		}
		
		$config['items'] = $items;
		
		if($config['multiple']) {
			$config['multiple'] = 'multiple';
		}
		else {
			unset($config['multiple']);
		}
		
		$html = new HTML();
		return $html->select($config);
	}
}

?>