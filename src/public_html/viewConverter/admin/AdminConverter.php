<?php

abstract class viewConverter_admin_AdminConverter extends viewConverter_ViewConverter
{
	/**
	 * required properties: title, breadcrumbs.
	 * optional properties: showLogoutLink, bannerLinkActive
	 */
	function __construct() {
		parent::__construct();
		
		$this->showLogoutLink = true;
		$this->bannerLinkActive = true;
	}
	
	protected function head() {
		return <<<_
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/admin.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/summary.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/informationField.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/html.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/paymentChooser.less'))}
			
			{$this->HTML->script(array('src' => '/js/less.js'))}
			
			{$this->HTML->script(array('src' => '/js/dojo/reggie_admin.js'))}	
_;
	}
	
	protected function body() {
		return <<<_
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
					{$this->getBanner()}
				</div>	
				
				<table class="sub-header-links">
					<tr>
						<td>
							{$this->breadcrumbs}
						</td>
						<td style="text-align:right; padding:10px 20px 0 0;">
							{$this->getLogout()}
						</td>
					</tr>	
				</table>	
_;
	}
	
	private function getLogout() {
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
		
		return $logoutLink;
	}
	
	private function getBanner() {
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
		
		return $banner;
	}
} 

?>