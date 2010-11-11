<?php

class fragment_XhrTableForm extends template_Template
{
	private $url;
	private $action;
	private $rows;
	
	function __construct($url, $action, $rows) {
		$this->url = $url;
		$this->action = $action;
		$this->rows = $rows;	
	}
	
	public function html() {
		return <<< TEMPLATE

<script type="text/javascript">
	dojo.require("hhreg.xhrTableForm");
</script>

<form method="post" class="save-form" action="{$this->url}">
	<table class="xhr-table-form">
		{$this->rows}
		<tr>
			<td></td>
			<td>
				<div class="sub-divider"></div>
				
				<input type="hidden" name="a" value="{$this->action}"/>
				<input type="button" class="button" value="Save"/>
				
				<div class="xhr-save-success hide">
					<img src="/images/check.gif" alt="Success" title="Success"/>
				</div>
				<div class="xhr-validation-error hide">
					<img src="/images/caution_red.gif" alt="Validation Errors" title="Validation Errors"/>
					<span class="error-text">Please correct the above errors.</span>
				</div>
				<div class="xhr-save-error hide">
					<img src="/images/ex.gif" alt="Error" title="Error"/>
					<span class="error-text">There was a problem saving. Please try again.</span>
				</div>
			</td>
		</tr>
	</table>
</form>
		
TEMPLATE;
	}
}
?>