<?php

class logic_admin_emailTemplate_Base extends PHPUnit_Framework_TestCase
{
	protected static $event;
	
	public static function setUpBeforeClass() {
		$eventId = db_EventManager::getInstance()->createEvent(array(
			'code' => 'test'.substr(time(), -9),
			'displayName' => 'Test Event',
			'regOpen' => date(db_Manager::$DATE_FORMAT),
			'regClosed' => date(db_Manager::$DATE_FORMAT, time()+1000)
		));
		
		self::$event = db_EventManager::getInstance()->find($eventId);
	}
}

?>