<?php

class fragment_reg_regOptionGroup_RegOptionGroups extends template_Template
{
	private $groups;
	private $regTypeId;
	private $selectedOptions; // map (model_ContentType::$REG_OPTION_<reg group id>) -> (<reg option id>)
	
	function __construct($groups, $regTypeId, $selectedOpts) {
		parent::__construct();

		$this->groups = $groups;
		$this->regTypeId = $regTypeId;
		$this->selectedOptions = $selectedOpts;
	}
	
	public function html() {
		$html = '';
		
		foreach($this->groups as $group) {
			$groupTemplate = new fragment_reg_regOptionGroup_RegOptionGroup($group, $this->regTypeId, $this->selectedOptions);
			$html .= $groupTemplate->html();
		}
		
		return $html;
	}
}

?>