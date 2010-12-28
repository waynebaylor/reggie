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
				<h3>Edit Registrations</h3>
				
				{$this->getRegistrants()}
			</div>
_;
	}
	
	private function getRegistrants() {
		$html = '';
		
		foreach($this->group['registrations'] as $index => $r) {
			$num = $index+1;
			
			$html .= <<<_
				<div style="background-color:#ccc; padding:5px; margin-bottom:10px; font-size:1.2em;">
					Registrant {$num}
				</div>			
_;

			$fragment = new fragment_editRegistrations_Registration($this->event, $this->group, $r);
			$html .= $fragment->html();
		}
		
		return $html;
	}
}

?>