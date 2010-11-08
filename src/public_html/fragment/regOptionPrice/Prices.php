<?php

class fragment_regOptionPrice_Prices extends template_Template
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct();
		
		$this->option = $option;
		$this->event = $event;
	}
	
	public function html() {
		$list = new fragment_regOptionPrice_List($this->event, $this->option);
		$add = new fragment_regOptionPrice_Add($this->event, $this->option);
		
		return <<<_
			<div class="fragment-reg-option-prices">
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