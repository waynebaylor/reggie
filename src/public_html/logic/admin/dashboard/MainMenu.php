<?php

class logic_admin_dashboard_MainMenu extends logic_Performer
{
	function __construct() {
		parent::__construct();
	}
	
	public function view($user) {
		$info = array(
			'userIsAdmin' => SecurityUtil::isAdmin($user),
			'events' => array()
		);		
		
		if($info['userIsAdmin']) {
			foreach(db_EventManager::getInstance()->getAllActive() as $event) {
				$info['events'][] = array(
					'status' => 'active',
					'event' => $event
				);
			}
			
			foreach(db_EventManager::getInstance()->getAllUpcoming() as $event) {
				$info['events'][] = array(
					'status' => 'upcoming',
					'event' => $event
				);
			}
			
			foreach(db_EventManager::getInstance()->getAllInactive() as $event) {
				$info['events'][] = array(
					'status' => 'inactive',
					'event' => $event
				);
			}
			
			$info['users'] = db_UserManager::getInstance()->findAll();
		}
		else {
			foreach(db_EventManager::getInstance()->getUserActive($user) as $event) {
				$info['events'][] = array(
					'status' => 'active',
					'event' => $event
				);
			}
			
			foreach(db_EventManager::getInstance()->getUserUpcoming($user) as $event) {
				$info['events'][] = array(
					'status' => 'upcoming',
					'event' => $event
				);
			}
			
			foreach(db_EventManager::getInstance()->getUserInactive($user) as $event) {
				$info['events'][] = array(
					'status' => 'inactive',
					'event' => $event
				);
			}
		}
		
		return $info;
	}
	
	public function addEvent($user, $eventInfo) {
		$id = db_EventManager::getInstance()->createEvent($eventInfo);
		db_UserManager::getInstance()->setEvent($user, array('id' => $id));
		
		$event = db_EventManager::getInstance()->find($id);
		
		FileUtil::createEventDir($event);
		
		return $this->view($user);
	}
	
	public function addUser($currentUser, $user) {
		if(!SecurityUtil::isAdmin($currentUser)) {
			throw new Exception("User: {$currentUser['email']} does not have 'Admin' role.");
		}
		
		db_UserManager::getInstance()->createUser($user);
		
		return $this->view($currentUser);
	}
	
	public function removeUser($currentUser, $userId) {
		if(!SecurityUtil::isAdmin($currentUser)) {
			throw new Exception("User: {$currentUser['email']} does not have 'Admin' role.");
		}
		
		$user = $this->strictFindById(db_UserManager::getInstance(), $userId);
		
		db_UserManager::getInstance()->deleteUser($user);
		
		return $this->view($currentUser);
	}
}

?>