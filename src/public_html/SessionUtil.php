<?php

class SessionUtil
{
	public static function getUser() {
		return empty($_SESSION['admin_user'])? NULL : $_SESSION['admin_user'];
	}
	
	public static function setUser($user) { 
		$_SESSION['admin_user'] = $user;
	}
	
	/**
	 * Refresh the session user with the latest from the database. This can only 
	 * be used if the session is already populated with a user. 
	 */
	public static function refreshUser() {
		$user = self::getUser();
		
		$refreshedUser = db_UserManager::getInstance()->find($user['id']);
		
		self::setUser($refreshedUser);
	}
	
	public static function getRequestStartTime() {
		return $_SERVER['REQUEST_TIME'];
	}
}
?>