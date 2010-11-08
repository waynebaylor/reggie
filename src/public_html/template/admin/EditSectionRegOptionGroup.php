<?php

class template_admin_EditSectionRegOptionGroup extends template_AdminPage
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct('Edit Registration Option Group');
		
		$this->event = $event;
		$this->group = $group;
	}
	
	protected function getContent() {
		$action = model_Group::isSectionGroup($this->group)? 
			'/action/admin/regOption/SectionRegOptionGroup' : 
			'/action/admin/regOption/RegOptionGroup';
		
		$edit = new fragment_sectionRegOptionGroup_Edit($this->group, $action);
		$options = new fragment_sectionRegOption_RegOptions($this->event, $this->group);
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'OptionGroup',
			'id'       => $this->group['id'],
			'eventId' => $this->event['id'],
			'isSectionGroup' => model_Group::isSectionGroup($this->group)
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.editSectionRegOptionGroup");
			</script>
			
			<div id="content">
				{$edit->html()}

				<div class="divider"></div>
				
				{$options->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
}

?>