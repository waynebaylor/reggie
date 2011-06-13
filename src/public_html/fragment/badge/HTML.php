<?php

class fragment_badge_HTML
{
	public static function selectByEventId($eventId, $config = array()) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'badgeTemplateType');		
		$config['value'] = ArrayUtil::getValue($config, 'value', '');
		
		$items = array();
		$types = model_BadgeTemplateType::values();
		foreach($types as $type) {
			$items[] = array(
				'label' => HTML::escapeHtml($type['name']),
				'value' => $type['code']	
			);
		}
		
		$config['items'] = $items;
		
		$html = new HTML();
		return  $html->select($config);
	}
}

?>