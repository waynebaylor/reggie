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
		
		$this->showUsersMenu = model_Role::userHasRole(SessionUtil::getUser(), array(
			model_Role::$SYSTEM_ADMIN, 
			model_Role::$USER_ADMIN
		))? 'true' : 'false';
		$this->showEventsMenu = model_Role::userHasRole(SessionUtil::getUser(), array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN,
			model_Role::$EVENT_MANAGER,		
	   		model_Role::$EVENT_REGISTRAR,	
	   		model_Role::$VIEW_EVENT	
		))? 'true' : 'false';
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
			{$this->HTML->css(array('rel' => 'stylesheet', 'href' => '/js/dojox/grid/enhanced/resources/EnhancedGrid.css'))}
			{$this->HTML->css(array('rel' => 'stylesheet', 'href' => '/js/dojox/grid/enhanced/resources/claro/EnhancedGrid.css'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/admin.less'))}
			{$this->HTML->css(array('rel' => 'stylesheet/less', 'href' => '/css/shared.less'))}
			
			{$this->HTML->script(array('src' => '/js/less.js'))}
			
			{$this->HTML->script(array('src' => '/js/dojo/reggie_admin.js'))}	
_;
	}
	
	protected function body() {
		return <<<_
				<script type="text/javascript">
					dojo.addOnLoad(function() { 
						dojo.require("dijit.MenuBar");
						dojo.require("dijit.MenuBarItem");
						dojo.require("hhreg.util");
						dojo.require("hhreg.admin.widget.ActionMenuBar");
						dojo.require("dijit.form.Textarea");
						
						dojo.query("textarea.expanding").forEach(function(item) {
							var ta = new dijit.form.Textarea({
								name: item.name, 
								style: "min-height:75px; width:500px;"
							}, item);
							ta.startup();
						});
						
						new hhreg.admin.widget.ActionMenuBar({
							showUsers: {$this->showUsersMenu},
							showEvents: {$this->showEventsMenu}
						}, dojo.place("<div></div>", dojo.byId("general-menu"), "replace")).startup();
						
						dojo.query("#user-menu").forEach(function(item) {
							var m = new dijit.MenuBar({}, dojo.byId("user-menu"));
							m.addChild(new dijit.MenuBarItem({
								label:"Logout",
								onClick: function() {
									window.location.href = hhreg.util.contextUrl('/admin/Login?a=logout');
								}
							}));
							m.startup();	
						});
					});
				</script>
		
				<div id="header">
					{$this->getBanner()}
				</div>	
				
				<table style="border-collapse:collapse;"><tr>
					<td style="width:100%;"><div id="general-menu"></div></td>
					<td><div id="user-menu"></div></td>
				</tr></table>
_;
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