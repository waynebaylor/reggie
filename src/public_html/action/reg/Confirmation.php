<?php

class action_reg_Confirmation extends action_BaseAction
{
	private $event;
	private $regGroup;

	function __construct($event, $regGroup) {
		parent::__construct();

		$this->event = $event;
		$this->regGroup = $regGroup;
	}

	public function view() {
		$confirmation = new fragment_reg_ConfirmationPage($this->event, $this->regGroup);
		
		// the user is finished, so we don't need the session anymore.
		session_destroy();
		
		return new template_reg_BasePage(array(
			'event' => $this->event,
			'title' => 'Confirmation',
			'id' => model_reg_RegistrationPage::$CONFIRMATION_PAGE_ID,
			'page' => $confirmation,
			'showMenu' => false,
			'showControls' => false
		));	
	}
}

?>