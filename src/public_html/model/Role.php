<?php

class model_Role
{
	public static $SYSTEM_ADMIN = 1;	// full access to everything
	public static $USER_ADMIN = 2;		// full access to users
	public static $EVENT_ADMIN = 3;		// full access to events
	public static $EVENT_MANAGER = 4;	// full access to specified event
	public static $EVENT_REGISTRAR = 5;	// access to registration features for specified event
	public static $VIEW_EVENT = 6;		// access to reports and attendee summary for specified event
	
	
	/**
	 * Check if user has at least one of the given roles.  
	 * 
	 * @param array $user 
	 * @param number $roleId
	 */
	public static function userHasRole($user, $roleIds) {
		if(!is_array($roleIds)) {
			$roleIds = array($roleIds);
		}
		
		foreach($user['roles'] as $role) {
			if(in_array($role['id'], $roleIds)) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Check if user has at least one of the given roles for the given event.
	 *
	 * @param array $user
	 * @param array $roleIds
	 * @param number $eventId
	 */
	public static function userHasRoleForEvent($user, $roleIds, $eventId) {
		if(!is_array($roleIds)) {
			$roleIds = array($roleIds);
		}
		
		foreach($user['roles'] as $role) {
			if(in_array($role['id'], $roleIds) && $eventId == $role['eventId']) {
				return true;
			}
		}
		
		return false;
	}
}

?>