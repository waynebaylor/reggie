<?php

class fragment_groupRegistration_field_Fields extends template_Template
{
	private $event;
	
	function __construct($event) {
		parent::__construct();
		
		$this->event = $event;
	}
	
	public function html() {
		$list = new fragment_groupRegistration_field_List($this->event);
		$add = new fragment_groupRegistration_field_Add($this->event);
		
		return <<<_
			<div class="fragment-fields">
				<div>
					{$list->html()}
				</div>
				
				<div class="sub-divider"></div>
				
				{$add->html()}
			</div>
_;
	}
}

?>