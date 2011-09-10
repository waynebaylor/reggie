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
	
	public function getView($properties) {
		$this->eventId = 0;
		$this->showEventMenu = 'false';
		$this->actionMenuEventLabel = '';
		$this->showReportMenu = 'false';
		$this->showRegFormMenu = 'false';
		$this->showBadgeTemplateMenu = 'false';
		$this->showFileMenu = 'false';
		$this->showPageMenu = 'false';
		$this->showCreateReg = 'false';
		
		$this->setProperties($properties);
		
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
		
		if(!empty($this->eventId)) {
			$this->showEventMenu = 'true';
			$this->showReportMenu = $this->getShowReportMenu(SessionUtil::getUser(), $this->eventId);
			$this->showRegFormMenu = $this->getShowRegFormMenu(SessionUtil::getUser(), $this->eventId);
			$this->showBadgeTemplateMenu = $this->getShowBadgeTemplateMenu(SessionUtil::getUser(), $this->eventId);
			$this->showFileMenu = $this->getShowFileMenu(SessionUtil::getUser(), $this->eventId);
			$this->showPageMenu = $this->getShowPageMenu(SessionUtil::getUser(), $this->eventId);
			$this->showCreateReg = $this->getShowCreateReg(SessionUtil::getUser(), $this->eventId);
		}
		
		return parent::getView($properties);
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
					dojo.require("dijit.MenuBar");
					dojo.require("dijit.MenuBarItem");
					dojo.require("hhreg.util");
					dojo.require("hhreg.admin.widget.ActionMenuBar");
					dojo.require("dijit.form.Textarea");
					
					dojo.addOnLoad(function() { 
						setTimeout(function() {
							dojo.query(dojo.byId("page-render-time")).orphan();
						}, 3000);
						
						dojo.query("textarea.expanding").forEach(function(item) {
							var ta = new dijit.form.Textarea({
								name: item.name, 
								style: "min-height:75px; width:500px;"
							}, item);
							ta.startup();
						});
						
						new hhreg.admin.widget.ActionMenuBar({
							showUsers: {$this->showUsersMenu},
							showEvents: {$this->showEventsMenu},
							showEventMenu: {$this->showEventMenu},
							eventLabel: "{$this->actionMenuEventLabel}",
							showReports: {$this->showReportMenu},
							showRegForm: {$this->showRegFormMenu},
							showBadgeTemplates: {$this->showBadgeTemplateMenu},
							showFiles: {$this->showFileMenu},
							showPages: {$this->showPageMenu},
							showCreateReg: {$this->showCreateReg},
							eventId: {$this->eventId}
						}, dojo.place("<div></div>", dojo.byId("action-menu-bar"), "replace")).startup();
					});
				</script>
		
				<div id="header">
					{$this->getBanner()}
				</div>	
				
				<div id="action-menu-bar"></div>
				
				<div id="page-render-time" style="position:fixed;bottom:0;background-color:#333;color:#aaa;">Page Rendered in {$this->pageRenderTime()}s</div>
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
				'href' => '/admin/Login',
				'parameters' => array(
					'a' => 'view'
				)
			));
		}
		
		return $banner;
	}
	
	private function getShowReportMenu($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR,
			model_Role::$VIEW_EVENT
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}

	private function getShowRegFormMenu($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}

	private function getShowBadgeTemplateMenu($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}

	private function getShowFileMenu($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}

	private function getShowPageMenu($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}
	
	private function getShowCreateReg($user, $eventId) {
		$showMenu = model_Role::userHasRole($user, array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN
		));
		
		$showMenu = $showMenu || model_Role::userHasRoleForEvent($user, array(
			model_Role::$EVENT_MANAGER,
			model_Role::$EVENT_REGISTRAR
		), $eventId);
		
		return $showMenu? 'true' : 'false';
	}
} 

?>