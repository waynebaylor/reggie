<?php

class fragment_XhrTableForm extends template_Template
{
	private $url;
	private $action;
	private $rows;
	private $buttonText;
	private $errorText;
	private $useAjax;
	
	function __construct($url, $action, $rows, $buttonText = 'Save', 
		$errorText = 'There was a problem saving. Please try again.', $useAjax = true) {
		parent::__construct();
		
		$this->url = $url;
		$this->action = $action;
		$this->rows = $rows;	
		$this->buttonText = $buttonText;
		$this->errorText = $errorText;
		$this->useAjax = $useAjax;
	}
	
	public function html() {
		return <<<_
			<script type="text/javascript">
				dojo.require("hhreg.xhrTableForm");
			</script>
			
			<form method="post" name="{$this->action}" action="{$this->contextUrl($this->url)}">
				{$this->HTML->hidden(array(
					'name' => 'useAjax',
					'value' => $this->useAjax? 'true' : 'false'
				))}
				
				<table class="xhr-table-form">
					{$this->rows}
					<tr>
						<td></td>
						<td>
							<div class="sub-divider"></div>
							
							{$this->HTML->hidden(array(
								'name' => 'a',
								'value' => $this->action
							))}

							<input type="button" class="button" value="{$this->buttonText}">
							
							<div class="xhr-save-success hide">
								{$this->HTML->img(array(
									'src' => '/images/check.gif',
									'alt' => 'Success',
									'title' => 'Success'
								))}
							</div>
							<div class="xhr-validation-error hide">
								{$this->HTML->img(array(
									'src' => '/images/caution_red.gif',
									'alt' => 'Validation Errors',
									'title' => 'Validation Errors'
								))}
								<span class="error-text">Please correct the above errors.</span>
							</div>
							<div class="xhr-save-error hide">
								{$this->HTML->img(array(
									'src' => '/images/ex.gif',
									'alt' => 'Error',
									'title' => 'Error'
								))}
								<span class="error-text">{$this->errorText}</span>
							</div>
						</td>
					</tr>
				</table>
			</form>
		
_;
	}
}
?>