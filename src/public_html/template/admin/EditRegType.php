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
	
	protected function getBreadcrumbs() {
		return new fragment_Breadcrumb(array(
			'location' => 'RegType',
			'regTypeId' => $this->regType['id']
		));
	}
	
	protected function getContent() {
		$edit = new fragment_regType_Edit($this->regType);
		
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				{$edit->html()}
			</div>
_;
	}
	
	
}

?>