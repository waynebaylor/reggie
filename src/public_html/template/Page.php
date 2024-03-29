<?php

abstract class template_Page extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	protected function head() {}
	
	protected function body() {}
	
	public function html() {
		header('Content-Type: text/html; charset=utf-8');
		
		return <<<_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>

<head>
	{$this->HTML->css(array('href' => '/js/dojo/resources/dojo.css'))}
	{$this->HTML->css(array('href' => '/js/dijit/themes/dijit.css'))}
	{$this->HTML->css(array('href' => '/js/dijit/themes/claro/claro.css'))}
	{$this->HTML->css(array('href' => '/js/dijit/themes/claro/document.css'))}
	{$this->HTML->css(array('href' => '/js/dojox/form/resources/BusyButton.css'))}
			
	{$this->HTML->script(array('src' => '/js/dojo/dojo.js'))}
	
	<style type="text/css">
		#script-enabled-content {
			visibility: hidden;
		}
		
		noscript p {
			color: black;
			font-size: 2em;
			font-weight: bold;
			text-align: center;
		}
	</style>

	{$this->head()}
	
	<script type="text/javascript">
		dojo.addOnLoad(function() {
			var messages = dojo.byId("xhr-response");
			if(messages) {
				dojo.require("hhreg.validation");
				hhreg.validation.showMessages(dojo.fromJson(messages.value), messages.form);
			}
		});
		
		document.write('<style type="text/css"> #script-enabled-content { visibility: visible; } </style>');
	</script>
</head>

<body class="claro">
	<noscript>
		<p>
			This site requires JavaScript. Please enable JavaScript in your browser.
		</p>
	</noscript>
	
	<div id="script-enabled-content">
		{$this->HTML->hidden(array(
			'id' => 'reggie.contextPath',
			'name' => 'reggie.contextPath',
			'value' => $this->contextUrl('/')
		))}
	
		{$this->body()}
	</div>
</body>

</html>
_;
	}
}

?>