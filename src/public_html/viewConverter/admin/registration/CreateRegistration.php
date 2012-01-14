<?php

class viewConverter_admin_registration_CreateRegistration extends viewConverter_admin_AdminConverter
{
	function __construct() {
		parent::__construct();
		
		$this->title = 'Create Registration';
	}
	
	protected function body() {
		$body = parent::body();
		
		$body .= <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrEditForm");
			</script>
			
			<div id="content">
				<div class="fragment-edit">
					<h3>{$this->title}</h3>
					{$this->xhrTableForm(array(
						'url' => '/admin/registration/CreateRegistration',
						'action' => 'createRegistration',
						'buttonText' => 'Continue',
						'useAjax' => false,
						'rows' => $this->getFileContents('page_admin_registration_CreateRegistration')
					))}
				</div>
			</div>
_;
		
		return $body;
	}
	
	public function getCreateRegistration($properties) {
		$this->setProperties($properties);
		
		return new template_Redirect("/admin/registration/Registration?eventId={$this->eventId}&id={$this->regGroupId}");
	}
}

?>