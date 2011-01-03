<?php 

abstract class template_AdminPage extends template_Page
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
	
	protected function head() {
		return <<<_
			<title>{$this->title}</title>
	
			{$this->HTML->css(array('href' => '/js/dojo/resources/dojo.css'))}
			{$this->HTML->css(array('href' => '/js/dijit/themes/dijit.css'))}
			{$this->HTML->css(array('href' => '/js/dijit/themes/claro/claro.css'))}
			
			{$this->HTML->css(array(
				'rel' => 'stylesheet/less',
				'href' => '/css/admin.less'))
			}
			
			{$this->HTML->css(array(
				'rel' => 'stylesheet/less',
				'href' => '/css/informationField.less'))
			}
		
			{$this->HTML->script(array('src' => '/js/less.js'))}
			{$this->HTML->script(array('src' => '/js/dojo/dojo.js'))}
			
			<script type="text/javascript">
				dojo.registerModulePath("hhreg", "{$this->contextUrl('/js/hhreg')}");
				dojo.require("hhreg");
			</script>
_;
	}
	
	protected function body() {
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
			$banner = $this->HTML->link(array(
				'label' => $banner,
				'href' => '/admin/MainMenu',
				'parameters' => array(
					'a' => 'view'
				)
			));
		}
		
		return <<<_
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
_;
	}
	
	protected function getContent() {
		return '';
	}
	
	protected abstract function getBreadcrumbs();
}

?>