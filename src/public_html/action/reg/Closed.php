<?php

class action_reg_Closed extends action_BaseAction
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function view() {
		$html = new fragment_reg_Closed($this->event);
		
		session_destroy();
		
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => $this->event['displayName'],
			'id' => 'closed',
			'page' => $html,
			'showMenu' => false,
			'showControls' => false
		));
	}
}

?>