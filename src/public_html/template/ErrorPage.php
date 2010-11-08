<?php

class template_ErrorPage extends template_Template
{
	function __construct() {
		parent::__construct();
	}	
	
	public function html() {
		return <<<_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Error</title>
</head>
<body style="background-color:#fe8;">
	<input id="xhr-response" type="hidden" name="error" value="true"/>
	
	<div style="padding-top:50px; text-align:center; color:red; font-size:60px; font-weight:bold;">
		<span style="border:6px solid black;">&nbsp;!&nbsp;</span>	
	</div>
	<div style="padding:20px; text-align:center;">
		<h1>There was a problem performing your request.</h1>
	</div>
</body>
</html>
_;
	}
}
	
?>