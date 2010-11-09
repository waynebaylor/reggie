<?php

class template_admin_EditSectionRegOption extends template_AdminPage
{
	private $option;
	private $event;
	
	function __construct($event, $option) {
		parent::__construct('Edit Registration Option');
		
		$this->event = $event;
		$this->option = $option;	
	}

	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'RegOption',
			'id' => $this->option['id'],
			'eventId' => $this->event['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_sectionRegOption_Edit($this->option);  
		$prices = new fragment_regOptionPrice_Prices($this->event, $this->option);
		$groups = new fragment_regOptionGroup_RegOptionGroups($this->event, $this->option);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
				dojo.require("hhreg.admin.regOptionPrices");
				dojo.require("hhreg.admin.regOptionGroups");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$prices->html()}
				
				<div class="divider"></div>
				
				{$groups->html()}
			</div>
_;
	}
}

?>