<?php 

abstract class template_AdminPage extends template_Template
{
	private $title;
	
	function __construct($t) {
		parent::__construct();
		
		$this->title = $t;
	}
	
	public function html() {
		$logoutLink = '';
		$user = SessionUtil::getUser();
		if(!empty($user)) { 
			$logoutLink = $this->HTML->link(array(
				'label' => "Logout",
				'href' => '/action/admin/Login',
				'parameters' => array(
					'a' => 'logout'
				),
				'title' => "Logout {$user['email']}"
			));
		}
		
		return <<<_
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>{$this->title}</title>
	<link rel="stylesheet" type="text/css" href="/js/dojo/resources/dojo.css"/>
	<link rel="stylesheet" type="text/css" href="/js/dijit/themes/dijit.css"/>
	<link rel="stylesheet" type="text/css" href="/js/dijit/themes/tundra/tundra.css"/>
	
	<link rel="stylesheet/less" type="text/css" href="/css/admin.less">

	<script type="text/javascript" src="/js/less.js"></script>
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
			<a href="/action/admin/MainMenu?action=view">
				Registration System
			</a>
		</div>	
		
		<table class="sub-header-links"><tr>
		<td>
			{$this->getBreadcrumbs()->html()}
		</td>
		<td style="text-align:right; padding:10px 20px 0 0;">
			{$logoutLink}
		</td>
		</tr></table>
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