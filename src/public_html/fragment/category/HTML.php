<?php

class fragment_category_HTML
{
	public static function radios($config = array()) {
		$html = new HTML();
		return $html->radios(self::getConfig($config));
	}	
	
	public static function checkboxes($config = array()) {
		$html = new HTML();
		return $html->checkboxes(self::getConfig($config));
	}	
	
	private static function getConfig($config) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'categoryId');
		
		if(empty($config['value'])) {
			$config['value'] = array();
		}
		
		$categoryItems = array();
		foreach(model_Category::values() as $cat) {
			$categoryItems[] = array(
				'label' => $cat['displayName'],
				'value' => $cat['id']
			);
		}
		
		$config['items'] = $categoryItems;
		
		return $config;
	}
}




?>