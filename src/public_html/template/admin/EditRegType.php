<?php

class template_admin_EditRegType extends template_AdminPage
{
	private $event;
	private $regType;
	
	function __construct($event, $regType) {
		parent::__construct('Edit '.$regType['description'].' Reg Type');
		
		$this->event = $event;
		$this->regType = $regType;
	}
	
	protected function getContent() {
		$edit = new fragment_regType_Edit($this->regType);
		$breadcrumbs = new fragment_Breadcrumb(array(
			'location' => 'RegType',
			'id' => $this->regType['id'],
			'eventId' => $this->event['id']
		));
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.editRegType");
			</script>
			
			<div id="content">
				{$edit->html()}
				
				<div class="divider"></div>
				
				{$breadcrumbs->html()}
			</div>
_;
	}
	
	
}

?>