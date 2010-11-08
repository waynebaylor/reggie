<?php

class fragment_event_Events extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		$list = new fragment_event_List();
		$add = new fragment_event_Add();
		
		return <<<_
			<div class="fragment-events">
				<div>
					{$list->html()}
				</div>
				
				<div class="sub-divider"></div>
				
				{$add->html()}
			</div>
_;
	}
}