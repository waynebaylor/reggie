<?php 

abstract class template_AdminPage extends template_Template
{
	private $title;
	private $showLogoutLink;
	private $bannerLinkActive;
	
	function __construct($t) {
		parent::__construct();
		
		if(!is_array($t)) {
			$t = array(
				'title' => $t,
				'showLogoutLink' => true,
				'bannerLinkActive' => true
			);
		}
		
		$this->title = $t['title'];
		$this->showLogoutLink = $t['showLogoutLink'];
		$this->bannerLinkActive = $t['bannerLinkActive'];
	}
	
	public function html() {
		$logoutLink = '';
		$user = SessionUtil::getUser();
		if(!empty($user) && $this->showLogoutLink) { 
			$logoutLink = $this->HTML->link(array(
				'label' => "Logout",
				'href' => '/admin/Login',
				'parameters' => array(
					'a' => 'logout'
				),
				'title' => "Logout {$user['email']}"
			));
		}
		
		$banner = 'Registration System';
		if($this->bannerLinkActive) {
			$banner = <<<_
				<a href="/admin/MainMenu?action=view">
					{$banner}
				</a>	
_;
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
			{$banner}
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