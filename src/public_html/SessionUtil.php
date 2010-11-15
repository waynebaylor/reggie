<?php

class SessionUtil
{
	public static function getAdminUser() {
		return empty($_SESSION['admin_user'])? NULL : $_SESSION['admin_user'];
	}
	
	public static function setAdminUser($user) {
		$_SESSION['admin_user'] = $user;
	}
}
?>