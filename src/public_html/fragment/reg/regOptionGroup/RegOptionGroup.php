<?php

class fragment_reg_regOptionGroup_RegOptionGroup extends template_Template
{
	private $group;
	
	function __construct($group) {
		parent::__construct();

		$this->group = $group;
	}
	
	public function html() {
		$optionTemplate = new fragment_reg_regOption_RegOptions($this->group);
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.reg.regOptionGroup");
			</script>
			
			<div class="reg-option-group">
				{$optionTemplate->html()}
			</div>
_;
	}
}

?>