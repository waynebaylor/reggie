<?php

class viewConverter_admin_regOption_RegOptionGroup extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Edit Registration Option Group';
	}
	
	protected function body() {
		$body = parent::body();
		
		$action = model_RegOptionGroup::isSectionGroup($this->group)? 
			'/admin/regOption/SectionRegOptionGroup' : 
			'/admin/regOption/RegOptionGroup';
		
		$edit = new fragment_sectionRegOptionGroup_Edit($this->group, $action);
		$options = new fragment_sectionRegOption_RegOptions($this->event, $this->group);
		
		$breadcrumbs = new fragment_Breadcrumbs($this->breadcrumbsParams);
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.editSectionRegOptionGroup");
			</script>
			
			{$breadcrumbs->html()}
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$options->html()}
			</div>
_;
		
		return $body;
	}
	
	public function getAddGroup($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionGroup_List($this->event, $this->option);
	}
	
	public function getRemoveGroup($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionGroup_List($this->event, $this->option);
	}
	
	public function getMoveGroupUp($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionGroup_List($this->event, $this->option);
	}
	
	public function getMoveGroupDown($properties) {
		$this->setProperties($properties);
		return new fragment_regOptionGroup_List($this->event, $this->option);
	}
	
	public function getSaveGroup($properties) {
		$this->setProperties($properties);
		return new fragment_Success();
	}
}

?>