<?php

class fragment_AjaxError extends template_Template
{
	public function html() {
		return <<<_
			<div style="background-color:red;">
				<h3>Error</h3>
			</div>
_;
	}
}

?>