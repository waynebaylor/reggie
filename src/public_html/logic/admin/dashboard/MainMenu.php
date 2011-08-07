<?php

class logic_admin_dashboard_MainMenu extends logic_Performer
{
	function __construct() {
		parent::__construct(); 
	}
	
	public function view($user) {
		$info = array(
			'events' => array()
		);		
		
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
		
		return $info;
	}
	
	public function addEvent($user, $eventInfo) {
		$id = db_EventManager::getInstance()->createEvent($eventInfo);
		db_UserManager::getInstance()->setEvent($user, array('id' => $id));
		
		$event = db_EventManager::getInstance()->find($id);
		
		FileUtil::createEventDir($event);
		
		return $this->view($user);
	}
	
	public function createRegistration($params) {
		$eventInfo  = db_EventManager::getInstance()->findInfoById($params['eventId']);
		$category = model_Category::valueOf($params['categoryId']);
		
		$regLogic = new logic_admin_registration_Registration();
		$info = $regLogic->createNewRegistration($eventInfo['id'], $category['id']);
		
		return array(
			'eventId' => $eventInfo['id'],
			'regGroupId' => $info['regGroupId'],
			'registrationId' => $info['registrationId']
		);
	}
}

?>