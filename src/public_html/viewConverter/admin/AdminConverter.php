<?php

/**
 * 
 * Presentation base class for administrative pages.
 * 
 * @author wtaylor
 *
 */
abstract class viewConverter_admin_AdminConverter extends viewConverter_ViewConverter
{
	function __construct() {
		parent::__construct();
		
		$this->showLogoutLink = true;
		$this->bannerLinkActive = true;
	}
	
	/**
	 * Returns the HTML for displaying the breadcrumbs associated with this page.
	 * @return string
	 */
	protected function getBreadcrumbs() {
		return '';	
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
						dojo.require("dijit.form.Textarea");
						dojo.query("textarea").forEach(function(item) {
							var ta = new dijit.form.Textarea({
								name: item.name, 
								style: "min-height:75px; width:500px;"
							}, item);
							ta.startup();
						});
					});
				</script>
		
				<div id="header">
					{$this->getBanner()}
				</div>	
				
				<table class="sub-header-links">
					<tr>
						<td>
							{$this->getBreadcrumbs()}
						</td>
						<td style="text-align:right;">
							{$this->getLogout()}
						</td>
					</tr>	
				</table>	
_;
	}
	
	/**
	 * Returns the HTML for the Logout Link.  
	 * @return string
	 */
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
	
	/**
	 * Returns the HTML for the page's banner.
	 * @return string
	 */
	private function getBanner() {
		$banner = 'Registration System';
		
		if($this->bannerLinkActive) {
			$banner = $this->HTML->link(array(
				'label' => $banner,
				'href' => '/admin/dashboard/MainMenu',
				'parameters' => array(
					'a' => 'view'
				)
			));
		}
		
		return $banner;
	}
} 

?>