<?php 

abstract class template_AdminPage extends template_Template
{
	private $title;
	
	function __construct($t) {
		parent::__construct();
		
		$this->title = $t;
	}
	
	public function html() {
		return <<<_
		
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>{$this->title}</title>
	<link rel="stylesheet" type="text/css" href="/js/dojo/resources/dojo.css"/>
	<link rel="stylesheet" type="text/css" href="/js/dijit/themes/dijit.css"/>
	<link rel="stylesheet" type="text/css" href="/js/dijit/themes/tundra/tundra.css"/>
	<link rel="stylesheet" type="text/css" href="/css/style.css"/>
	<script type="text/javascript" src="/js/dojo/dojo.js"></script>
	<script type="text/javascript" src="/js/hhreg.js"></script>
</head>
<body class="tundra">
	<div id="body">
		<script type="text/javascript">
			dojo.addOnLoad(function() {
				// cancel button
				if(dojo.byId("cancelButton")) {
					dojo.connect(dojo.byId("cancelButton"), "onclick", function() {
						history.back();
					});
				}
			});
		</script>

		<div id="header">
			<a href="/action/MainMenu?action=view">
				Registration System
			</a>
		</div>	
		
		{$this->getBreadcrumbs()->html()}
		
		{$this->getContent()}
	</div>
</body>
</html>
		
_;
	}
	
	protected function getContent() {
		return '';
	}
	
	protected abstract function getBreadcrumbs();
}

?>