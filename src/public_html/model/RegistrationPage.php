<?php

class model_RegistrationPage
{
	public static $PAYMENT_PAGE_ID = 'payment';
	
	public static $SUMMARY_PAGE_ID = 'summary';
	
	public static $CONFIRMATION_PAGE_ID = 'confirmation';
	
	public static function isViewable($event, $pageId) {
		$category = model_RegSession::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);
		
		// you have to complete the last reg page before getting to the payment page.
		if(self::$PAYMENT_PAGE_ID === $pageId) {
			return in_array($pages[count($pages)-1]['id'], model_RegSession::getCompletedPages());
		}
		// you must complete the payment page (if any) before getting to the summary page.
		else if(self::$SUMMARY_PAGE_ID === $pageId) {
			// if the event doesn't have any payment types, then we just need to check if
			// the last page has been completed.
			if(empty($event['paymentTypes'])) {
				return self::isViewable($event, self::$PAYMENT_PAGE_ID);	
			}
			else {
				return in_array(self::$PAYMENT_PAGE_ID, model_RegSession::getCompletedPages());
			}
		} 
		// you must complete the summary page before getting to the confirmation page.
		else if(self::$CONFIRMATION_PAGE_ID === $pageId) {
			return in_array(self::$SUMMARY_PAGE_ID, model_RegSession::getCompletedPages());
		}
		// the reg pages must be completed in order.
		else {
			foreach($pages as $index => $page) {  
				if(intval($pageId, 10) === intval($page['id'], 10)) {
					// you can view it if it's the first page or you've finished the previous page.
					return $index === 0 || in_array($pages[$index-1]['id'], model_RegSession::getCompletedPages());
				}
			}
			
			return false;
		}
	}
	
	public static function getFirstPage($event) {
		$category = model_RegSession::getCategory();

		$pages = model_EventPage::getVisiblePages($event, $category);

		if(isset($pages[0])) {
			return $pages[0];
		}

		throw new Exception('The event: "'.$event['code'].'" has no pages visible to the category: "'.$category['displayName'].'"');
	}
	
	public static function isLastRegistrationPage($event, $pageId) {
		$category = model_RegSession::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);
		$count = count($pages);
		
		return intval($pageId, 10) === intval($pages[$count-1]['id'], 10);
	}
	
	public static function getPrevPage($event, $pageId) {
		$category = model_RegSession::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);

		foreach($pages as $index => $p) {
			if(intval($p['id'], 10) === intval($pageId, 10)) {
				// can't go before the first page.
				return $pages[max(0, $index-1)];
			}
		}

		throw new Exception('There is no page before: "'.$pageId.'" (page id)'); 
	}
	
	public static function getNextPage($event, $pageId) {
		$category = model_RegSession::getCategory();
		$pages = model_EventPage::getVisiblePages($event, $category);
		$count = count($pages);
		
		foreach($pages as $index => $p) {
			if(intval($p['id'], 10) === intval($pageId, 10)) {
				if($index < $count-1) {
					return $pages[$index+1];
				}
			}	
		}
		
		throw new Exception('There is no registration page after: "'.$pageId.'" (page id)');
	}
}

?>