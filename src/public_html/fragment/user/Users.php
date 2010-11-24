<?php

class fragment_user_Users extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	public function html() {
		$list = new fragment_user_List();
		$add = new fragment_user_Add();
		
		return <<<_
			<div class="fragment-users">
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