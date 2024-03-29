<?php

class fragment_XhrAddForm extends template_Template
{
	private $link;
	private $url;
	private $action;
	private $rows;
	
	function __construct($link, $url, $action, $rows) {
		parent::__construct();
		
		$this->link = $link;	
		$this->url = $url;
		$this->action = $action;
		$this->rows = $rows;
	}
	
	public function html() {
		return <<<_

<div class="xhr-add-form">
	<span class="add-link link">{$this->link}</span>
	<div class="add-form hide">
		<form method="post" action="{$this->contextUrl($this->url)}">
			<table>
				{$this->rows}
				<tr>
					<td></td>
					<td>
						<div class="sub-divider"></div>
						{$this->HTML->hidden(array(
							'name' => 'a',
							'value' => $this->action
						))}

						<input type="button" class="button" value="Continue">
						<span class="cancel-link link">Cancel</span>
						
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
							<span class="error-text">There was a problem saving. Please try again.</span>
						</div>
					</td>
				</tr>
			</table>		
		</form>
	</div>
</div>
		
_;
	}
}

?>