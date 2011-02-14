<?php

class fragment_category_HTML
{
	public static function radios($config = array()) {
		$config['name'] = ArrayUtil::getValue($config, 'name', 'categoryId');
		
		$categoryItems = array();
		foreach(model_Category::values() as $cat) {
			$categoryItems[] = array(
				'label' => $cat['displayName'],
				'value' => $cat['id']
			);
		}
		
		$html = new HTML();
		
		return $html->radios(array(
			'name' => $config['name'],
			'items' => $categoryItems
		));
	}	
}




?>