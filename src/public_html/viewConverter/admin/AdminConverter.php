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
		
		$this->showCreateEvent = model_Role::userHasRole(SessionUtil::getUser(), array(
			model_Role::$SYSTEM_ADMIN,
			model_Role::$EVENT_ADMIN,
			model_Role::$EVENT_MANAGER
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
						dojo.query("textarea.expanding").forEach(function(item) {
							hhreg.util.enhanceTextarea(item);
						});
						
						//////////////////////////////
						// setup breadcrumbs (this must come before setting up main menu)
						//////////////////////////////
						
						var breadcrumbHtml = dojo.query("input.breadcrumb").map(function(item) {
							return {label: item.name, href: hhreg.util.contextUrl(item.value)};
						}).map(function(item) {
							return dojo.string.substitute('<td onclick="document.location=\'\${href}\'">\${label}</td>', item);
						}).join('');
						
						if(breadcrumbHtml) {
							breadcrumbHtml = '<div id="breadcrumb-bar"><table><tr>'+breadcrumbHtml+'</tr></table></div>';
							dojo.place(breadcrumbHtml, dojo.byId("action-menu-bar"), "after");
						}
						
						//////////////////////////////
						// setup main menu
						//////////////////////////////
						
						new hhreg.admin.widget.ActionMenuBar({
							showUsers: {$this->showUsersMenu},
							showEvents: {$this->showEventsMenu},
							showCreateEvent: {$this->showCreateEvent},
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
				
				<div id="page-render-time" style="display:none;">Page Rendered in {$this->pageRenderTime()}s</div>
_;
	}
	
	/**
	 * Returns the HTML for the page's banner.
	 * @return string
	 */
	private function getBanner() {
		$banner = <<<_
			{$this->HTML->img(array(
				'src' => '/images/cm_logo.jpg',
				'style' => 'vertical-align: top; border: 1px solid #fff;',
				'alt' => 'Conference Managers Logo'
			))}
			Registration System
_;
		
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
		$a = new action_admin_report_ReportList();
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}

	private function getShowRegFormMenu($user, $eventId) {
		$a = new action_admin_event_EditEvent();
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}

	private function getShowBadgeTemplateMenu($user, $eventId) {
		$a = new action_admin_badge_BadgeTemplates();
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}

	private function getShowFileMenu($user, $eventId) {
		$a = new action_admin_fileUpload_FileUpload();
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}

	private function getShowPageMenu($user, $eventId) {
		$a = new action_admin_staticPage_PageList();		
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}
	
	private function getShowCreateReg($user, $eventId) {
		$a = new action_admin_registration_CreateRegistration();
		return $a->hasRole($user, $eventId)? 'true' : 'false';
	}
} 

?>