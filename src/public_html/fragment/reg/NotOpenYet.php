<?php

class fragment_reg_NotOpenYet extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$open = date_format(date_create($this->event['regOpen']), 'd M, Y');
		$closed = date_format(date_create($this->event['regClosed']), 'd M, Y');
		
		return <<<_
			<p>The page you requested is not yet available.</p>
			<p> 
				{$this->event['displayName']} will be available from <strong>{$open}</strong> to <strong>{$closed}</strong>.
			</p>
_;
	}
}

?>