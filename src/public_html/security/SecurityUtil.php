<?php 

class security_SecurityUtil
{
	public static function isAdmin($user) {
		$user = SessionUtil::getAdminUser();
		return $user['isAdmin'] === 'true';
	}
	
	public static function hasEvent($user, $event) {
		$eventIds = db_UserManager::getInstance()->findEventIds($user);
		return in_array($event['id'], $eventIds);
	}	
}

?>