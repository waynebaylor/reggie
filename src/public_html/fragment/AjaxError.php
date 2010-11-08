<?php

require_once 'template/Template.php';

class fragment_AjaxError extends template_Template
{
	public function html() {
		return <<< TEMPLATE

<div style="background-color:red;">
	<h3>Error</h3>
</div>
		
TEMPLATE;
	}
}

?>