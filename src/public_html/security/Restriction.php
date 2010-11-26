<?php

class security_Restriction
{
	public static $EVENT = 'event';
	
	public static $USER = 'user';
	
	/**
	 * Perform security permission check. If check fails an exception will be thrown.
	 */
	public static function check($info) {
		switch($info['type']) {
			case self::$EVENT:
				if(!self::event($info['user'], $info['eventId'])) {
					throw new Exception("User does not have permission to access event: (userId, eventId) -> ({$info['user']['id']}, {$info['eventId']}).");
				}
				break;
			case self::$USER:
				
				break;
			default:
				throw new Exception("Unknown security restriction: '{$info['type']}'.");
				break;
		}	
	}
	
	/**
	 * Checks if user has access to the given event. 
	 * 
	 */
	private static function event($user, $eventId) {
		return SecurityUtil::hasEventPermission($user, $eventId);
	}
	
	/**
	 * Checks if user has access to the given user.  
	 *
	 */
	private static function user($user, $userId) {
		
	}
}

?>