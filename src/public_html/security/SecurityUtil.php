<?php 

class security_SecurityUtil
{
	public static function isAdmin($user) {
		$user = SessionUtil::getUser();
		return $user['isAdmin'] === 'true';
	}
	
	public static function hasEventPermission($user, $event) {
		$eventIds = db_UserManager::getInstance()->findEventPermissions($user);
		return in_array($event['id'], $eventIds);
	}	
}

?>