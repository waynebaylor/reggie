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
	{$this->head()}
</head>
<body class="tundra">
	{$this->HTML->hidden(array(
		'id' => 'reggie.contextPath',
		'name' => 'reggie.contextPath',
		'value' => $this->contextUrl('/')
	))}
	
	{$this->body()}
</body>
</html>
_;
	}
}

?>