<?php

class fragment_event_List extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.list");
			</script>
			
			<h3>Events</h3>
			
			<div class="fragment-list">
				<table class="admin">
					{$this->getAllRows()}
				</table>	
			</div>
_;
	}
	
	private function getAllRows() {
		$user = SessionUtil::getUser();
		
		if(SecurityUtil::isAdmin($user)) {
			$active = db_EventManager::getInstance()->getAllActive();
			$upcoming = db_EventManager::getInstance()->getAllUpcoming();
			$inactive = db_EventManager::getInstance()->getAllInactive();
		}
		else {
			$active = db_EventManager::getInstance()->getUserActive($user);
			$upcoming = db_EventManager::getInstance()->getUserUpcoming($user);
			$inactive = db_EventManager::getInstance()->getUserInactive($user);
		}
		
		if(empty($active) && empty($upcoming) && empty($inactive)) {
			return <<<_
				<tr><td>No Events</td></tr>
_;
		}

		return $this->getRows($active, 'Active').
			   $this->getRows($upcoming, 'Upcoming').
			   $this->getRows($inactive, 'Inactive');
	}
	
	private function getRows($events, $status) {
		$html = '';
		
		foreach ($events as $event) {
			$html .= <<<_
				<tr>
					<td>
						{$event['displayName']} ({$event['code']})
					</td>
					<td>
						{$status}
					</td>
					<td>
						{$this->getLinks($event)}
					</td>
					<td>
						{$this->HTML->link(array(
							'label' => 'Edit',
							'href' => '/action/admin/event/EditEvent',
							'parameters' => array(
								'action' => 'view',
								'id' => $event['id']
							),
							'title' => 'Edit Event'
						))}
						{$this->HTML->link(array(
							'label' => 'Reports',
							'href' => '/action/admin/report/Report',
							'parameters' => array(
								'action' => 'eventReports',
								'id' => $event['id']
							),
							'title' => 'Event Reports'
						))}
						{$this->HTML->link(array(
							'label' => 'Files',
							'href' => '/action/admin/fileUpload/FileUpload',
							'parameters' => array(
								'action' => 'view',
								'id' => $event['id']
							),
							'title' => 'Event Files'
						))}
					</td>
_;
		}

		return $html;
	}
	
	private function getLinks($event) {
		$html = '';
		
		$categories = model_Category::values();
		foreach($categories as $category) {
			$visiblePages = model_EventPage::getVisiblePages($event, $category);
			if(!empty($visiblePages)) {
				$code = model_Category::code($category);
				$html .= $this->HTML->link(array(
					'label' => $category['displayName'],
					'href' => "/event/{$event['code']}/{$code}",
					'title' => 'As seen by '.$category['displayName'],
					'target' => '_blank'
				));
			}
		}
		
		return $html;
	}
}

?>