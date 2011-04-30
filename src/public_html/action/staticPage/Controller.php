<?php

class action_staticPage_Controller
{
	function __construct() {
		$this->logic = new logic_staticPage_Controller();
		$this->converter = new viewConverter_staticPage_Controller();
	}
	
	public function view($props) {
		$info = $this->logic->view(array(
			'eventCode' => $props['eventCode'],
			'name' => $props['name']
		));
		
		$template = $this->converter->getView($info);
		
		echo $template->html();
	}
}


?>