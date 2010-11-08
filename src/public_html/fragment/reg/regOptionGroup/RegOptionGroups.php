<?php

class fragment_reg_regOptionGroup_RegOptionGroups extends template_Template
{
	private $groups;
	
	function __construct($groups) {
		parent::__construct();

		$this->groups = $groups;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->groups as $group) {
			$groupTemplate = new fragment_reg_regOptionGroup_RegOptionGroup($group);
			$html .= $groupTemplate->html();
		}
		
		return $html;
	}
}

?>