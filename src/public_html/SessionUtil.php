<?php

class SessionUtil
{
	public static function getUser() {
		return empty($_SESSION['admin_user'])? NULL : $_SESSION['admin_user'];
	}
	
	public static function setUser($user) { 
		$_SESSION['admin_user'] = $user;
	}
}
?>