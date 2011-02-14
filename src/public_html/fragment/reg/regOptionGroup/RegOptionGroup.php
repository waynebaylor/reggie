<?php

class fragment_reg_regOptionGroup_RegOptionGroup extends template_Template
{
	private $group;
	private $regTypeId;
	private $selectedOptions;
	
	function __construct($group, $regTypeId, $selectedOpts, $pageId) {
		parent::__construct();

		$this->group = $group;
		$this->regTypeId = $regTypeId;
		$this->selectedOptions = $selectedOpts;
		$this->pageId = $pageId;
	}
	
	public function html() {
		$optionTemplate = new fragment_reg_regOption_RegOptions($this->group, $this->regTypeId, $this->selectedOptions, $this->pageId);
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