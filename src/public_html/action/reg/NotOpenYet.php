<?php

class action_reg_NotOpenYet extends action_BaseAction
{
	private $event;

	function __construct($event) {
		parent::__construct();

		$this->event = $event;
	}
	
	public function view() {
		$html = new fragment_reg_NotOpenYet($this->event);
		
		session_destroy();
		
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => $this->event['displayName'],
			'id' => 'not_open_yet',
			'page' => $html,
			'showMenu' => false,
			'showControls' => false
		));
	}
}

?>