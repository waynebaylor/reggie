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
	
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/admin.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/shared.less'))}
			
			{$this->HTML->script(array('src' => '/js/less.js'))}
			
			{$this->HTML->script(array('src' => '/js/dojo/reggie_admin.js'))}
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
				'href' => '/admin/Login',
				'parameters' => array(
					'a' => 'view'
				)
			));
		}
		
		return <<<_
			<div id="body">
				<div id="header">
					{$banner}
				</div>	
				
				<table class="sub-header-links"><tr>
					<td>
						{$this->getBreadcrumbs()->html()}
					</td>
					<td style="text-align:right;">
						{$logoutLink}
					</td>
				</tr></table>
				
				{$this->getContent()}
				
				<div class="divider"></div>
			</div>
_;
	}
	
	protected function getContent() {
		return '';
	}
	
	protected function getBreadcrumbs() {
		return '';
	}
}

?>