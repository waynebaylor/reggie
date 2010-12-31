<?php

class fragment_regOptionGroup_RegOptionGroups extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->event = $event;
		$this->option = $option;
	}
	
	public function html() {
		$list = new fragment_regOptionGroup_List($this->event, $this->option);
		$add = new fragment_regOptionGroup_Add($this->event, $this->option);
		
		return <<<_
			<div class="fragment-reg-option-groups">
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