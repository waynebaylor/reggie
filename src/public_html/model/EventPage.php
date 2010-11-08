<?php

class model_EventPage
{
	public static function isVisibleTo($page, $category) {
		foreach($page['visibleTo'] as $pageCat) {
			if(intval($category['id'], 10) === intval($pageCat['id'], 10)) {
				return true;
			}
		}
		
		return false;
	}
	
	public static function getVisiblePages($event, $category) {
		$visiblePages = array();
		
		$pages = $event['pages'];
		foreach($pages as $page) {
			if(model_EventPage::isVisibleTo($page, $category)) {
				$visiblePages[] = $page;
			}
		}
		
		return $visiblePages;
	}
}

?>