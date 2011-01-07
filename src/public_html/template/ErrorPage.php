<?php

class template_ErrorPage extends template_Page
{
	function __construct() {
		parent::__construct();
	}	
	
	public function html() {
		$html = parent::html();
		return str_replace('body class="tundra"', 'body class="error"', $html);
	}
	
	protected function head() {
		return <<<_
			<title>Error</title>	

			<style type="text/css">
				.error {
					background-color: #fe8;
				}
				
				.mark {
					padding-top: 50px; 
					text-align: center; 
					color: red; 
					font-size: 60px; 
					font-weight: bold;
				}
				
				.mark span {
					border: 6px solid black;
				}
				
				.msg {
					padding: 20px; 
					text-align: center;
				}
			</style>
_;
	}
	
	protected function body() {
		return <<<_
			<div class="error">
				<textarea id="xhr-response" class="hide" name="error">true</textarea>
				
				<div class="mark">
					<span>&nbsp;!&nbsp;</span>	
				</div>
				<div class="msg">
					<h1>There was a problem performing your request.</h1>
				</div>
			</div>
_;
	}
}
	
?>