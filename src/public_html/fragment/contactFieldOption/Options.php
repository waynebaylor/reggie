<?php

require_once 'template/Template.php';
require_once 'fragment/contactFieldOption/List.php';
require_once 'fragment/contactFieldOption/Add.php';

class fragment_contactFieldOption_Options extends template_Template
{
	private $field;
	private $event;
	
	function __construct($event, $field) {
		$this->field = $field;	
		$this->event = $event;
	}
	
	public function html() {
		$list = new fragment_contactFieldOption_List($this->event, $this->field);
		$add = new fragment_contactFieldOption_Add($this->event, $this->field);
		
		return <<<_
			<div class="fragment-options">
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