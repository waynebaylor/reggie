<?php

class fragment_sectionRegOption_RegOptions extends template_Template
{
	private $group;
	private $event;
	
	function __construct($event, $group) {
		parent::__construct();

		$this->event = $event;
		$this->group = $group;
	}
	
	public function html() {
		$list = new fragment_sectionRegOption_List($this->event, $this->group);
		$add = new fragment_sectionRegOption_Add($this->event, $this->group);
		
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