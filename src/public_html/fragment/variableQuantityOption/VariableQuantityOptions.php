<?php

class fragment_variableQuantityOption_VariableQuantityOptions extends template_Template
{
	private $event;
	private $section;
	
	function __construct($event, $section) {
		parent::__construct();
		
		$this->event = $event;
		$this->section = $section;
	}
	
	public function html() {
		$list = new fragment_variableQuantityOption_List($this->event, $this->section);
		$add = new fragment_variableQuantityOption_Add($this->event, $this->section);
		
		return <<<_
			<div class="fragment-variable-quantity-options">
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