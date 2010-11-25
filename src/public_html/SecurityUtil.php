<?php 

class SecurityUtil
{
	public static function isAdmin($user) {
		return $user['isAdmin'] === 'true';
	}
	
	public static function hasEvent($user, $eventId) {
		$eventIds = db_UserManager::getInstance()->findEventIds($user);
		return in_array($eventId, $eventIds);
	}	 
	
	public static function hasEventPermission($user, $eventId) {
		return self::isAdmin($user) || self::hasEvent($user, $eventId);
	}
}

?>