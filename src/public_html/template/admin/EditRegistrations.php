<?php

class template_admin_EditRegistrations extends template_AdminPage
{
	private $event;
	private $group;
	
	function __construct($event, $group) {
		parent::__construct('Edit Registrations');
		
		$this->event = $event;
		$this->group = $group;
	}
	
	protected function getBreadcrumbs() {
		return new fragment_Empty();
	}
	
	protected function getContent() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				<h3>Edit Registration</h3>
				
				{$this->getRegistrants()}
			</div>
_;
	}
	
	private function getRegistrants() {
		$html = '';
		
		foreach($this->group['registrations'] as $r) {
			$html .= $this->getRegistration($r);
		}
		
		return $html;
	}
	
	private function getRegistration($registration) {
		return print_r($registration, true);
	}
}

?>