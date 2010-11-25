<?php

class security_Restriction
{
	public static $EVENT = 'event';
	
	public static $USER = 'user';
	
	
	/**
	 * Checks if user has access to the given event. 
	 * 
	 */
	public static function event($user, $eventId) {
		if(!SecurityUtil::hasEventPermission($user, $eventId)) {
			throw new Exception("User does not have permission to access event. (userId, eventId) -> ({$user['id']}, {$eventId})");
		}
		
		return true;
	}
	
	/**
	 * Checks if user has access to the given user.  
	 *
	 */
	public static function user($user, $userId) {
		
	}
}

?>