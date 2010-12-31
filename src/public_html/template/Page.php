<?php

abstract class template_Page extends template_Template
{
	function __construct() {
		parent::__construct();
	}
	
	protected function head() {}
	
	protected function body() {}
	
	public function html() {
		return <<<_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<style type="text/css">
		noscript p {
			color: black;
			font-size: 2em;
			font-weight: bold;
			text-align: center;
		}
	</style>
	
	{$this->head()}
</head>
<body class="tundra">
	{$this->HTML->hidden(array(
		'id' => 'reggie.contextPath',
		'name' => 'reggie.contextPath',
		'value' => $this->contextUrl('/')
	))}
	
	<noscript>
		<p>
			This site requires JavaScript. Please enable JavaScript in your browser.
		</p>
	</noscript>
	
	<div id="script-enabled-content" style="display:none;">
		<script type="text/javascript">
			document.getElementById("script-enabled-content").style.display = "";
		</script>
		
		{$this->body()}
	</div>
</body>
</html>
_;
	}
}

?>