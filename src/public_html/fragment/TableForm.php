<?php

class fragment_TableForm extends template_Template
{
	private $url;
	private $action;
	private $rows;
	private $buttonText;
	
	function __construct($url, $action, $rows, $buttonText) {
		parent::__construct();
		
		$this->url = $url;
		$this->action = $action;
		$this->rows = $rows;	
		$this->buttonText = $buttonText;
	}
	
	public function html() {
		return <<<_
			<form method="post" name="{$this->action}" action="{$this->contextUrl($this->url)}">
				<table class="table-form">
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
						</td>
					</tr>
				</table>
			</form>
		
_;
	}
}
?>